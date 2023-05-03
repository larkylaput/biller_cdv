<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode692 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // PNB Credit Cards
            // $mainField = preg_replace('/\D/', '', $mainField); // remove all the non numberic characters
            if(
                $this->validateLength($mainField) and
                $this->validateCharacters($mainField) and
                $this->validateNumberic($mainField)
            ) {
                return true;
            }

        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateLength($mainField)
    {
        $length = strlen($mainField);
        if ($length <> 11) {
            return false;
        }
        return true;
    }

    private function validateNumberic($mainField) {
        return is_numeric($mainField);
    }

    private function validateCharacters($mainField)
    {
        $accountNumber = str_split($mainField);
        $TalGetDigit = 10;
        $totalDigit = 0;

        for($i = 0; $i < count($accountNumber) - 1; $i++){
            $SubGetDigit = intval($accountNumber[$i] + $TalGetDigit) % 10;

            $formula['SubGetDigit'][] = "($accountNumber[$i] + $TalGetDigit) % 10 == $SubGetDigit";

            if($SubGetDigit == 0){
                $TalGetDigit = intval(10 * 3) % 11;

                $formula['TalGetDigit'][] = "(10 * 3) % 11 == $TalGetDigit";
            }
            else{
                $TalGetDigit = intval($SubGetDigit * 3) % 11;

                $formula['TalGetDigit'][] = "($SubGetDigit * 3) % 11 == $TalGetDigit";
            }
        }

        $totalDigit += intval(11 - $TalGetDigit) % 10;
        if($totalDigit == intval($accountNumber[10])){
            return true;
        }
        else{
            return false;
        }
    }
}