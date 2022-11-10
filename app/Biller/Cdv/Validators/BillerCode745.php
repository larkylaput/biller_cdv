<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode745 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // Dragon Pay
            $mainField = preg_replace('/\D/', '', $mainField);
            return $this->validateLength($mainField, 10) && $this->validateCheckDigit($mainField);
        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
    }

    // To check the length of input
    function validateLength($input, $length)
    {
        return strlen($input) === $length;
    }

    function validateCheckDigit($mainfield)
    {
        $account_number = substr($mainfield, 0, 9);
        $check_digit = intval(substr($mainfield, - 1));

        //validate check digit
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

        return $result === $check_digit;
    }
}
