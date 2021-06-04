<?php

namespace App\Biller\Cdv\Validators;

use App\Modules\Biller\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode810 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // PLDT
            $mainField = preg_replace('/\D/', '', $mainField);
            return $this->validateLength($mainField, 10) && $this->validateCheckDigit($mainField);
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
    }

    function validateCheckDigit($mainfield)
    {
        $account_number = substr($mainfield, 0, 9); // Account Number
        $check_digit = substr($mainfield, - 1); // Check Digit

        // Split String to parse into array
        $digits = str_split($account_number);
        $sum = 0;
        $weights = "432765432";

        // Add all the products of account number and weights;
        foreach ($digits as $indx => $digit) $sum += ($digit * $weights[$indx]);

        // Get the remainder
        $modulo = $sum % 11;

        if ($modulo === 1) return false; // If 1 then considered as Invalid
        if ($modulo === 0) return $check_digit == 0; // If 0 then check digit must be 0

        // Otherwise subtract by 11 then compare to check digit
        $result = 11 - $modulo;
        return $result == $check_digit;
    }

    // To check the length of input
    function validateLength($input, $length)
    {
        return strlen($input) === $length;
    }
}
