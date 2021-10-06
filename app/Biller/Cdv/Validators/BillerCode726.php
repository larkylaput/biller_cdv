<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode726 implements BillerCdvInterface
{
    CONST EVEN_WEIGHT = [
        [9, 2],
        [8, 4],
        [7, 6],
        [6, 8],
        [5, 0],
        [4, 1],
        [3, 3],
        [2, 5],
        [1, 7],
        [0, 9],
    ];

    CONST P_WEIGHT = [
        '0123456789',
        '9876543210',
        '1234567890',
        '8901234567',
        '2345678901',
        '7890123456',
        '3456789012',
        '6789012345',
        '4567890123',
        '5678901234',
    ];

    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) && 
                $this->validateCharacters($mainField) &&
                $this->validateDate($mainField) &&
                $this->validateRange($mainField) &&
                $this->validateTwoLastDigit($mainField)
            ) {
                if($this->validateFormat($mainField, $amount)){
                    return true;
                }
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) == 16) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateDate($mainField) {
        $date = substr($mainField, 0, 6);
        $expiryDate = date('mdy');

        return ($date >= $expiryDate) ? true : false;
    }
    
    private function validateRange($mainField) {
        $range = substr($mainField, 6, 5);
        return ($range >= 1 AND $range <= 99999) ? true : false;
    }

    private function validateTwoLastDigit($mainField) {
        return (substr($mainField, 14, 2) == '17') ? true : false;
    }

    private function validateFormat($mainField, $amount)
    {
        $amount = number_format((float) $amount, 3, '.', '');

        $wholeNum = (int) $amount;
        $CheckWhole = $this->getCheck($wholeNum);

        $dec = substr($amount, -3);
        $CheckDecimal = $this->getCheck($dec);

        $WholeNumber = $amount * 1000;
        $WNMod = $WholeNumber % 7;
        $BindWDNum = $CheckWhole . $CheckDecimal . $WNMod;
        $FinalCheckamt = $this->getCheck($BindWDNum);

        $Final = substr($mainField, 11, 2);
        if($Final == $FinalCheckamt) {
            $GetRefNum = substr($mainField, 0, 13);

            $RNDS = $this->validateRefNo($GetRefNum, $mainField);
            if($RNDS == substr($mainField, 13, 1)) {
                return true;
            }
        }

        return false;
    }

    private function validateRefNo($GetRefNum, $mainField)
    {
        $RNCount = 0;
        $ROTA = 0;
        $CAFOP = 0;

        while ($RNCount < strlen($GetRefNum)) {
            $sum = substr($GetRefNum, $RNCount, 1);
            $formula['sum'][] = "$sum";

            $formula['ROTA'][] = "$ROTA + $sum = " . ($ROTA + $sum);
            $ROTA += $sum;

            $CCAFOP = substr($GetRefNum, $RNCount, 1);
            $formula['CCAFOP'][] = "$CCAFOP";

            if ($CCAFOP > 4) {
                $CAFOP += 1;
            } else {
                $CAFOP += 0;
            }
            $formula['CAFOP'][] = "$CAFOP";

            $RNCount += 2;
        }

        $ERNCount = 1;
        $EROTA = 0;
        while ($ERNCount < strlen($GetRefNum)) {
            $sum2 = substr($GetRefNum, $ERNCount, 1);
            $formula['sum2'][] = "$sum2";

            $formula['EROTA'][] = "$EROTA + $sum2 = " . ($ROTA + $sum2);
            $EROTA += $sum2;

            $ERNCount += 2;
        }

        $RNDS = (($ROTA * 2) + $CAFOP + $EROTA) % 10;
        $formula['RNDS'] = "(($ROTA * 2) + $CAFOP + $EROTA) % 10 = $RNDS";

        $formula['CheckDigit'] = $RNDS == substr($mainField, 13, 1);
        // dd($formula);
        return $RNDS;
    }

    private function getCheck($value)
    {
        $arrValue = str_split($value);
        $ODDTotal = 0;

        $W1Total = 0;
        $W2Total = 0;

        $ArrayHold = 0;

        for($i = 0; $i < count($arrValue); $i+=2) {
            $formula['ODD'][] = "$i // $ODDTotal += " . $arrValue[$i];
            $ODDTotal += $arrValue[$i];
        }

        for($i = 1; $i < count($arrValue); $i+=2) {
            $index = $arrValue[$i];

            $formula["WEIGHT INDEX"][] = $index;

            $formula['WEIGHT 1'][] = "$i // $W1Total += " . self::EVEN_WEIGHT[$index][0];
            $formula['WEIGHT 2'][] = "$i // $W2Total += " . self::EVEN_WEIGHT[$index][1];

            $W1Total += self::EVEN_WEIGHT[$index][0];
            $W2Total += self::EVEN_WEIGHT[$index][1];
        }

        for($i = 0; $i < count($arrValue); $i++) {
            if($i > 10){
                continue;
            }
            
            $index = strval($arrValue[$i]);

            $formula['ARRAYHOLD'][] = "$i // $ArrayHold += " . (strpos(self::P_WEIGHT[$i], $index) + 1);
            $ArrayHold += (strpos(self::P_WEIGHT[$i], $index) + 1);

            $formula["ARRAYHOLD INDEX"][] = $index;
            $formula["ARRAYHOLD SELECT"][] = self::P_WEIGHT[$i];
            $formula["ARRAYHOLD POSITION"][] = (strpos(self::P_WEIGHT[$i], $index) + 1);
        }

        $formula['ODD'][] = "TOTAL = $ODDTotal";
        $formula['WEIGHT 1'][] = "TOTAL = " . $W1Total;
        $formula['WEIGHT 2'][] = "TOTAL = " . $W2Total;
        $formula['ARRAYHOLD'][] = "TOTAL = " . $ArrayHold;

        $OEM = ($ODDTotal + $W1Total) % 10;
        $OEP = ($ODDTotal + $W2Total + $ArrayHold) % 7;
        $CHECK = $OEM . $OEP;

        $formula['OEM'][] = "$OEM = ($ODDTotal + $W1Total) % 10";
        $formula['OEP'][] = "$OEP = ($ODDTotal + $W2Total + $ArrayHold) % 7";
        $formula['CHECK'][] = "$CHECK = $OEM . $OEP";

        // dd($formula);
        return $CHECK;
    }
}
