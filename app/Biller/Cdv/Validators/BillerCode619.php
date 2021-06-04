<?php

namespace App\Biller\Cdv\Validators;

use App\Modules\Biller\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode619 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // INNOVE
            $mainField = preg_replace('/\D/', '', $mainField);
            return $this->validateLength($mainField, 9) && $this->validateRange($mainField, 100000000, 999999999) && $this->validateCheckDigit($mainField);
        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
    }

    function validateCheckDigit($mainfield)
    {
        $account_number = substr($mainfield, 0, 8); // Account Number
        $check_digit = substr($mainfield, - 1); // Check Digit

        $sum = 0;
        $weight = 1;
        $digits = array_reverse(str_split($account_number));

        // Get the sum of the products of digit and weight
        foreach ($digits as $indx => $digit) {
            $weight *= 2;
            $sum += (intval($digit) * $weight);
        }

        // Get the remainder
        $result = $sum % 11;

        error_log($result);

        if ($result > 9) return $check_digit == 0; //If the remainder is greater than 9 then check_digit must be 0

        // Otherwise, compare the check digit to result
        return $result == $check_digit;
    }

    // To check the length of input
    function validateLength($input, $length)
    {
        return strlen($input) === $length;
    }

    function validateRange($mainfield, $from, $to)
    {
        return $mainfield <= $to && $mainfield >= $from;
    }
}
