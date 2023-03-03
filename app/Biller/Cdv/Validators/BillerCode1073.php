<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1073 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool

    {
        try {
            //OB Montessori Center Inc.
            $mainField = preg_replace('/\D/', '', $mainField);
            return $this->validateLength($mainField, 9) 
            && $this->checkFirstDigit($mainField)
            && $this->checkNumeric($mainField);
        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
    }

    // To check the length of input
    public function validateLength($input, $length)
    {
        return strlen($input) === $length;
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
        $digit = substr($input,1,8);
        //dd($digit);
        return ctype_digit($digit);
    }
}
