<?php

namespace App\Biller\Cdv\Validators;

use App\Modules\Biller\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode808 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // Maynilad
            $mainField = preg_replace('/\D/', '', $mainField);
            return $this->validateLength($mainField, 8) && $this->validateRange($mainField, 50000595, 99999999) && $this->validateCheckDigit($mainField);
        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
    }

    public function validateCheckDigit($mainfield)
    {
        $account_number = substr($mainfield, 0, 7);
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

        //If the result check digit is 10, check digit should be zero.
        if ($result === 10) {
            $check_digit = 0;
        }

        return ($result == $check_digit);
    }

    // To check the range of input
    public function validateRange($input, $from, $to)
    {
        return $input <= $to && $input >= $from;
    }

    // To check the length of input
    public function validateLength($input, $length)
    {
        return strlen($input) === $length;
    }
}
