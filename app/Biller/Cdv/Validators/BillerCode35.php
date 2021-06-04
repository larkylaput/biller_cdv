<?php

namespace App\Biller\Cdv\Validators;

use App\Modules\Biller\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode35 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // HSBC Credit Card
            $mainField = preg_replace('/\D/', '', $mainField);
            return $this->validateLength($mainField, 16) && $this->validateBIN($mainField) && $this->validateCheckDigit($mainField);
        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
    }

    public function validateCheckDigit($mainfield)
    {
        $account_number = intval(substr($mainfield, 0, 15));
        $check_digit = intval(substr($mainfield, - 1));

        //validate check digit
        $digits = str_split($account_number);
        $sum = 0;
        foreach ($digits as $indx => $digit) {
            if ($indx % 2 === 0) {
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
        $result = $ceiling - $sum;
        return $result == $check_digit;
    }

    public function validateBIN($mainfield)
    {
        $bin = substr($mainfield, 0, 6);
        $allowed_bins = array(544757, 544758, 517790, 402892, 402893, 461984, 436367, 436524);
        return in_array($bin, $allowed_bins);
    }

    // To check the length of input
    public function validateLength($input, $length)
    {
        return strlen($input) === $length;
    }
}
