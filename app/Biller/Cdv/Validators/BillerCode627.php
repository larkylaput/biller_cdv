<?php

namespace App\Biller\Cdv\Validators;

use App\Modules\Biller\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode627 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // Smart/Sun
            $mainField = preg_replace('/\D/', '', $mainField);
            if ($this->validateLength($mainField, 10)) {
                return $this->validateSmartCMTSCab1($mainField) ||
                    $this->validateSmartCMTCab2($mainField) ||
                    $this->validateSmartLEC1($mainField) ||
                    $this->validateSmartLEC2($mainField) ||
                    $this->validateSmartInterim($mainField) ||
                    $this->validateSmartSIBS($mainField);
            } elseif ($this->validateLength($mainField, 7)) {
                return $this->validateSmartOldAlgo($mainField);
            }
            return false;
        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
    }

    public function validateLength($mainfield, $length)
    {
        return strlen($mainfield) === $length;
    }

    public function validateRange($mainfield, $from, $to)
    {
        return $mainfield <= $to && $mainfield >= $from;
    }

    public function validateSmartCMTSCab1($mainfield)
    {
        return $this->validateRange($mainfield, 0000000001, 9999999) &&
            $this->validateSmartCMTSCab1CheckDigit($mainfield);
    }

    public function validateSmartCMTCab2($mainfield)
    {
        return $this->validateRange($mainfield, 0000000001, 9999999) &&
            $this->validateSmartCMTCab2CheckDigit($mainfield);
    }

    public function validateSmartLEC1($mainfield)
    {
        return $this->validateRange($mainfield, 40000001, 49999999);
    }

    public function validateSmartLEC2($mainfield)
    {
        return $this->validateRange($mainfield, 50000001, 59999999) &&
            $this->validateSmartLEC2CheckDigit($mainfield);
    }

    public function validateSmartInterim($mainfield)
    {
        return $this->validateRange($mainfield, 70000001, 79999999) &&
            $this->validateSmartInterimCheckDigit($mainfield);
    }

    public function validateSmartSIBS($mainfield)
    {
        return ($mainfield >= 80000001) &&
            $this->validateSmartSIBSCheckDigit($mainfield);
    }

    public function validateSmartOldAlgo($mainfield)
    {
        return $this->validateSmartOldAlgoCheckDigit($mainfield);
    }

    public function validateSmartCMTSCab1CheckDigit($mainfield)
    {
        $account_number = substr($mainfield, 0, 9);
        $check_digit = intval(substr($mainfield, - 1));
        $digits = str_split($account_number);
        $sum = 0;
        foreach ($digits as $indx => $digit) {
            if (($indx + 1) % 2 !== 0) {
                $prod = ($digit * 2);
                if ($prod > 9) {
                    $splits = str_split($prod);
                    $prod = $splits[0] + $splits[1];
                }
                $sum = $sum + $prod;
            } else {
                $sum = $sum + $digit;
            }
        }

        $ceiling = ceil($sum / 10) * 10;
        $result = intval($ceiling - $sum);

        //if the sum obtained ends in 0, then check_digit = 0
        if (intval(substr($sum, - 1) === 0)) {
            $check_digit = 0;
        }

        return $result === $check_digit;
    }

    public function validateSmartCMTCab2CheckDigit($mainfield)
    {
        $account_number = substr($mainfield, 0, 9);
        $check_digit = intval(substr($mainfield, - 1));

        $digits = str_split($account_number);
        $sum = 0;
        foreach ($digits as $indx => $digit) {
            if (($indx + 1) % 2 !== 0) {
                $prod = ($digit * 2);
                if ($prod > 9) {
                    $splits = str_split($prod);
                    $prod = $splits[0] + $splits[1];
                }
                $sum = $sum + $prod;
            } else {
                $sum = $sum + $digit;
            }
        }

        $ceiling = ceil($sum / 10) * 10;
        $result = intval($ceiling - $sum);

        $result = ($result < 7) ? $result += 4 : $result -= 4;

        return $result === $check_digit;
    }

    public function validateSmartLEC2CheckDigit($mainfield)
    {
        $check_digit = intval(substr($mainfield, - 1));

        $addEleven = intval(substr($mainfield, 0, 9));
        $sum = $addEleven + 11;

        $result = $sum % 11;

        //If the result check digit is 10, check digit should be zero.
        if ($result === 10) {
            $check_digit = 0;
        }

        return $result === $check_digit;
    }

    public function validateSmartInterimCheckDigit($mainfield)
    {
        $account_number = substr($mainfield, 0, 9);
        $check_digit = intval(substr($mainfield, - 1));

        //validate check digit
        $digits = str_split($account_number);
        $sum = 0;
        foreach ($digits as $indx => $digit) {
            if (($indx + 1) % 2 === 0) {
                $prod = ($digit * 2);
                if ($prod > 9) {
                    $splits = str_split($prod);
                    $prod = $splits[0] + $splits[1];
                }
                $sum = $sum + $prod;
            } else {
                $sum = $sum + $digit;
            }
        }

        $ceiling = ceil($sum / 10) * 10;
        $result = (int)($ceiling - $sum);

        //if the sum obtained ends in 0, then check_digit = 0
        if (intval(substr($sum, - 1) === 0)) {
            $check_digit = 0;
        }

        return ($result == $check_digit);
    }

    public function validateSmartSIBSCheckDigit($mainfield)
    {
        $account_number = substr($mainfield, 0, 9);
        $check_digit = intval(substr($mainfield, - 1));

        $digits = str_split($account_number);
        $sum = 0;
        $weights = '432765432';

        foreach ($digits as $indx => $digit) {
            $sum += ($digit * $weights[$indx]);
        }
        $modu = $sum % 11;
        if ($modu == 0) {
            $check_digit = 0;
        }
        if ($modu == 1) {
            $check_digit = 9;
        }

        $result = intval(11 - $modu);

        return $result === $check_digit;
    }

    public function validateSmartOldAlgoCheckDigit($mainfield)
    {
        $account_number = substr($mainfield, 0, 6);
        $check_digit = intval(substr($mainfield, - 1));

        //validate check digit
        $digits = str_split($account_number);
        $sum = 0;
        foreach ($digits as $indx => $digit) {
            if (($indx + 1) % 2 === 0) {
                $prod = ($digit * 2);
                if ($prod > 9) {
                    $splits = str_split($prod);
                    $prod = $splits[0] + $splits[1];
                }
                $sum = $sum + $prod;
            } else {
                $sum = $sum + $digit;
            }
        }

        $ceiling = ceil($sum / 10) * 10;
        $result = intval($ceiling - $sum);

        //if the sum obtained ends in 0, then check_digit = 0
        if (intval(substr($sum, - 1) === 0)) {
            $check_digit = 0;
        }

        return $result === $check_digit;
    }
}
