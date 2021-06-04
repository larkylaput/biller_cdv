<?php

namespace App\Biller\Cdv\Validators;

use App\Modules\Biller\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode81 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // SKY CABLE
            $mainField = preg_replace('/\D/', '', $mainField);
            return $this->validateLength($mainField, 9) && $this->validateCheckDigit($mainField);
        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
    }

    function validateCheckDigit($mainfield)
    {
        $account_number = substr($mainfield, 0, 8); // Account Number
        $check_digit = substr($mainfield, - 1); // Check Digit


        // Split String to parse into array
        $digits = str_split($account_number);
        $sum = 0;
        $weights = "32765432";

        // Add all the products of account number and weights;
        foreach ($digits as $indx => $digit) $sum += ($digit * $weights[$indx]);

        // Get the remainder
        $modulo = $sum % 11;

        // Otherwise subtract by 11
        $result = 11 - $modulo;

        if ($result === 11) return $check_digit == 0; // If the difference is 11, check digit must be 0
        if ($result === 10) return false; // If the difference is 10, then considered as Invalid

        // Otherwise compare it to $check_digit
        return $result == $check_digit;
    }

    // To check the length of input
    function validateLength($input, $length)
    {
        return strlen($input) === $length;
    }
}
