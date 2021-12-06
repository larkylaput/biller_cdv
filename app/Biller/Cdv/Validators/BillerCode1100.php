<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1100 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool

    {
        try {
            //Southern Christian College
            //$mainField = preg_replace('/\D/', '', $mainField);
            return $this->validateLength($mainField, 5, 9) 
            && $this->checkFirstDigit($mainField)
            && $this->checkNumeric($mainField);
        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
    }

    // To check the length of input
    public function validateLength($input, $min, $max)
    {
        return (strlen($input) > $min) && (strlen($input) < $max);
    }
    
    // To check the first digit
    public function checkFirstDigit($input)
    {
        $digit = 1;
        return $input[0] == $digit;
    }

    // To check the numeric only
    public function checkNumeric($input)
    {
        return preg_match("/^[0-9]+$/",$input);
    }
}
