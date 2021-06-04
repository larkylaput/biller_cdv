<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode33 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // East West Bank Credit Cards
            $mainField = preg_replace('/\D/', '', $mainField);
            return $this->validateLength($mainField, 16) && $this->validateBIN($mainField) && $this->validateCheckDigit($mainField);
        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
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

    // To check the length of input
    public function validateLength($input, $length)
    {
        return strlen($input) === $length;
    }

    public function validateBIN($mainfield)
    {
        $bin = substr($mainfield, 0, 6);
        $allowed_bins = array("519149", "518886", "514921", "437550", "468767", "524224", "548924", "888888", "516637", "558999", "400992", "400993", "412611", "412612", "517988", "554550", "518096", "554650", "554601", "445086", "960801", "960802");
        return in_array($bin, $allowed_bins);
    }
}
