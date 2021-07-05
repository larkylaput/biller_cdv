<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1033 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // Mactan Electric Company
            $mainField = preg_replace('/\D/', '', $mainField);
            return $this->validateLength($mainField, 8) && $this->validateRange($mainField, 10000000, 39999999);
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }

        return true;
    }

    // To check the length of input
    public function validateLength($mainField, $length)
    {
        return strlen($mainField) === $length;
    }

    //To check if 1st 3 digit si with in 100 to 399
    function validateRange($mainfield, $from, $to)
    {
        return $mainfield <= $to && $mainfield >= $from;
    }

}
