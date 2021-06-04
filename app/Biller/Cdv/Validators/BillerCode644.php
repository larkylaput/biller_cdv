<?php

namespace App\Biller\Cdv\Validators;

use App\Modules\Biller\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode644 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // Globe
            $mainField = preg_replace('/\D/', '', $mainField);
            if ($this->validateLength($mainField, 10)) {
                return $this->validateCheckDigitForLength10($mainField);
            } elseif ($this->validateLength($mainField, 8)) {
                return $this->validateRange($mainField, 10000000, 99999999) && $this->validateCheckDigitForLength8($mainField);
            }
            return false;
        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
    }

    public function validateCheckDigitForLength10($mainfield)
    {
        $account_number = substr($mainfield, 0, 9); // Account Number
        $check_digit = substr($mainfield, - 1); // Check Digit

        // Split String to parse into array
        $digits = str_split($account_number);
        $sum = 0;

        // Add all the products of account number and weights;
        foreach ($digits as $indx => $digit) {
            $sum += ($digit * ($indx + 1));
        }

        // Get the remainder
        $result = $sum % 11;

        if ($result > 9) {
            return false;
        } // If the remainder is two digit then considered as Invalid

        // Otherwise compare to check digit
        return $result == $check_digit;
    }

    public function validateCheckDigitForLength8($mainfield)
    {
        $account_number = substr($mainfield, 0, 7); // Account Number
        $check_digit = substr($mainfield, - 1); // Check Digit

        $sum = 0;
        $weight = 1;
        $current = 10000000 + intval($account_number);

        // Get the sum and divide the current to 10 until the current will become 0 or less than 0
        while ($current > 0) {
            $weight ++;
            $sum += (($current % 10) * $weight);
            $current = intval($current / 10);
        }

        // Get the remainder and subtract it to 11
        $result = 11 - ($sum % 11);

        // Compare the check digit to result
        return $result == $check_digit;
    }

    // To check the length of input
    public function validateLength($input, $length)
    {
        return strlen($input) === $length;
    }

    // To check the range of input
    public function validateRange($input, $from, $to)
    {
        return $input <= $to && $input >= $from;
    }
}
