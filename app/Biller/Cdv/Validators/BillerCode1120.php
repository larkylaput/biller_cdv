<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1120 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // PNB Credit Cards
            // $mainField = preg_replace('/\D/', '', $mainField); // remove all the non numberic characters
            if(
                $this->validateLength($mainField)  and 
                ($this->validateCharacters($mainField) or 
                $this->checkSpecialCharacter($mainField))
            ) {
                return true;
            }

        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateLength($mainField)
    {
        $length = strlen($mainField);
        if ($length < 6 or $length > 8) {
            return false;
        }
        return true;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function checkSpecialCharacter($mainField){

        $mainField_substr = substr($mainField,0,8);
        $str_count = substr_count ($mainField_substr, '-');

       if($str_count){
        return true;
       }
       return false;

    }
    
}
