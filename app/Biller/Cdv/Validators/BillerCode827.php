<?php

namespace App\Biller\Cdv\Validators;

use App\Modules\Biller\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode827 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // PNB Credit Cards
            $mainField = preg_replace('/\D/', '', $mainField);
            return $this->validateLength($mainField, 16) && $this->validateBIN($mainField) && $this->validateCheckDigit($mainField);
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
    }

    // To check the length of input
    function validateLength($input, $length)
    {
        return strlen($input) === $length;
    }

    function validateBIN($mainfield)
    {
        $bin = substr($mainfield, 0, 6);
        $allowed_bins = array("512634", "513208", "540076", "545143", "552280", "533639", "622145", "533653", "524046", "488908", "625013", "521387", "553294", "543761", "548509", "488907");
        return in_array($bin, $allowed_bins);
    }

    function validateCheckDigit($mainfield)
    {
        $account_number = substr($mainfield, 0, 15); // Account Number
        $check_digit = substr($mainfield, - 1); // Check Digit

        // Split String to parse into array
        $digits = str_split($account_number);
        $sum = 0;
        $weights = "212121212121212";

        // Add all the products of account number and weights;
        foreach ($digits as $indx => $digit) {
            $prod = ($digit * $weights[$indx]);
            if ($prod > 9) {
                $splits = str_split($prod);
                $prod = intval($splits[0]) + intval($splits[1]);
            };
            $sum += $prod;
        }

        // If the ending of $sum is 0 then the check digit must be 0
        if (substr($sum, - 1) == 0) return $check_digit == 0;

        // Otherwise compare the check digit from the result
        $ceiling = ceil($sum / 10) * 10;
        $result = $ceiling - $sum;

        return $result == $check_digit;
    }
}
