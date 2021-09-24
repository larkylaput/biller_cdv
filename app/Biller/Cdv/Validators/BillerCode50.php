<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode50 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // Citibank VISA/Mastercard
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
        $bin2 = substr($mainfield, 0, 8);
        $allowed_bins = array(
            '453971','453972','453248','542339','540127','403418','403419','552097','522272',
            '524067','453913','453915','434348','453916','604872','445043','445044','445046',
            '555021','531229','486638','470586','436313','436575','436576','436577','445056',
            '524202','537726','549419','453914','445051','400699','445052','445045'
        );
        $allowed_bins2 = array(
            '00000076','00000078'
        );
        return in_array($bin, $allowed_bins) || in_array($bin2, $allowed_bins2);
    }
}
