<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1130 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) AND 
                $this->validateChars($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField)
    {
        $length = strlen($mainField);
        
        return $length === 6 ? true : false;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }

    private function validateChars($mainField){

        if(ctype_alpha(substr($mainField,0,3)) AND $this->validateCharacters(substr($mainField,3,3))){
            return true;

        }
        return false;

    }
}
