<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode1093 implements BillerCdvInterface{

    public function validate($mainField, $amount): bool{

        try {

            // Bohol Wisdom School
            //$mainField = preg_replace('/\D/', '', $mainField);
            return $this->validateLength($mainField, 7) && $this->validateDigit($mainField);

        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    //check the length of the mainfield
    public function validateLength($mainField, $length){

        return strlen($mainField) < $length;
    }

    // Numeric character only
    public function validateDigit($mainField){

        return preg_match("/^[0-9]+$/",$mainField);

    }

}
