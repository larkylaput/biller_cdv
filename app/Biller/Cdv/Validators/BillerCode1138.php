<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode1138 implements BillerCdvInterface{

    public function validate($mainField, $amount): bool{

        try {

            // Colegio De Sto. Tomas - Recoletos
            return $this->validateLength($mainField, 11) && $this->validateDigit($mainField);

        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    //check the length of the mainfield
    public function validateLength($mainField, $length){

        return strlen($mainField) < $length;
    }

    // Alphacharacter only
    public function validateDigit($mainField){

        return ctype_alnum($mainField);

    }

}
