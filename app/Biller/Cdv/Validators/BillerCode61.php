<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode61 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // BDO Credit Cards
            $mainField = preg_replace('/\D/', '', $mainField);
            if ($this->validateAmexBIN($mainField)) {
                return $this->validateLength($mainField, 15) && $this->validateAmexCheckDigit($mainField);
            } elseif ($this->validateBIN($mainField)) {
                return $this->validateLength($mainField, 16) && $this->validateCheckDigit($mainField);
            }
            return false;
        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
    }

    // To check the length of input
    public function validateLength($input, $length)
    {
        return strlen($input) === $length;
    }

    public function validateAmexBIN($mainfield)
    {
        $bin = substr($mainfield, 0, 6);
        $allowed_bins = array("377670", "377671", "377672", "377678", "377679", "377693");
        return in_array($bin, $allowed_bins);
    }

    public function validateBIN($mainfield)
    {
        $bin = substr($mainfield, 0, 6);
        $allowed_bins = array(
            "492101", "492164", "491110", "492161", "439196", "439197", "418358", "418359", "488957",
            "464995", "541781", "545635", "545636", "524326", "512571", "518869", "524301", "523994",
            "545275", "548095", "519963", "356277", "356278", "356232", "356233", "356867", "625033",
            "625035", "361001", "361002", "360999",
        );
        return in_array($bin, $allowed_bins);
    }

    public function validateCheckDigit($mainfield)
    {
        $account_number = substr($mainfield, 0, 15); // Account Number
        $check_digit = substr($mainfield, - 1); // Check Digit

        // Split String to parse into array
        $digits = str_split($account_number);
        $sum = 0;

        // Add all the products of account number and weights;
        foreach ($digits as $index => $digit) {
            if (($index + 1) % 2 !== 0) {
                $prod = ($digit * 2);
                if ($prod > 9) {
                    $splits = str_split($prod);
                    $prod = intval($splits[0]) + intval($splits[1]);
                }
                $sum = $sum + $prod;
            } else {
                $sum = $sum + $digit;
            }
        }


        // If the ending of $sum is 0 then the check digit must be 0
        if (substr($sum, - 1) == 0) {
            return $check_digit == 0;
        }

        // Otherwise compare the check digit from the result
        $ceiling = $sum % 10;
        $result = 10 - $ceiling;

        return $result == $check_digit;
    }

    public function validateAmexCheckDigit($mainfield)
    {
        $account_number = substr($mainfield, 0, 14); // Account Number
        $check_digit = substr($mainfield, - 1); // Check Digit

        // Split String to parse into array
        $digits = str_split($account_number);
        $sum = 0;

        // Add all the products of account number and weights;
        foreach ($digits as $index => $digit) {
            if (($index + 1) % 2 === 0) {
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

        // Otherwise compare the check digit from the result
        $ceiling = $sum % 10;
        $result = 10 - $ceiling;

        if ($result == 10) {
            return $check_digit == 0;
        }
        return $result == $check_digit;
    }
}
