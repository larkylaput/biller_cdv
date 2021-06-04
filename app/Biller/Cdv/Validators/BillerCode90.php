<?php

namespace App\Biller\Cdv\Validators;

use App\Modules\Biller\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode90 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // Bankard / RCBC
            $mainField = preg_replace('/\D/', '', $mainField);
            return $this->validateLength($mainField, 16) && $this->validateBIN($mainField) && $this->validateCheckDigit($mainField);
        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
    }

    function validateCheckDigit($mainfield)
    {
        $account_number = substr($mainfield, 0, 15);
        $check_digit = intval(substr($mainfield, - 1));

        $digits = str_split($account_number);
        $sum = 0;
        foreach ($digits as $indx => $digit) {
            //print("-".$digit);
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
        $result = (int)($ceiling - $sum);

        //If the result check digit is 10, check digit should be zero.
        if ($result === 10) {
            $check_digit = 0;
        }

        return ($result == $check_digit);
    }

    function validateBIN($mainfield)
    {
        $bin = substr($mainfield, 0, 6);
        $allowed_bins = array(544195, 545369, 547581, 517968, 517982, 541509, 541512, 524302, 553283, 552366, 429382, 431170, 457358, 436861, 427934, 356286, 356288, 356275, 356276, 625007, 917010);
        return in_array($bin, $allowed_bins);
    }

    // To check the length of input
    function validateLength($input, $length)
    {
        return strlen($input) === $length;
    }
}
