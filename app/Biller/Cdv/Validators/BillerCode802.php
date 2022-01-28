<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode802 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if(
                $this->validateLength($mainField) && 
                $this->validateCharacters($mainField) 
            ) {
                return true;
            }

        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }
    
    private function validateLength($mainField) {
        $length = strlen($mainField);
        return ($length == 14);
    }

    private function validateCharacters($mainField) {
        $substr = substr($mainField, 0, 3);
        return (in_array($substr, ['008', '009'])) ? true : false;
    }
}
