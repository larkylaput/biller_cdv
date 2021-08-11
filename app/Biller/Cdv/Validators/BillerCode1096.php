<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1096 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        
        try {
            $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) AND 
                $this->validatechars($mainField)
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
        
        return $length == 14 ? true : false;
    }
    
    private function validateChars($mainField){

        if(is_numeric(substr($mainField,1,14))){
            return true;
        }
        return false;

    }
}
