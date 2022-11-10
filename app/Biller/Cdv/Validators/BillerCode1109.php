<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1109 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // PNB Credit Cards
            // $mainField = preg_replace('/\D/', '', $mainField); // remove all the non numberic characters
            if(
                $this->validateLength($mainField) and 
                $this->checkString($mainField) and
                $this->validateSpecialCharacter($mainField)
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
        if ($length < 7 or $length > 8) {
            return false;
        }
        return true;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }
    
    private function validateSpecialCharacter($mainField){
        // $substr = substr($mainField,5,4); // 5,4 = 6789 
        $substr = substr($mainField,3,1); // 4,1 = 4 should be '-'
        if($substr == '-'){
            return true;
        }

        return false;
    }

    private function checkString($mainField){

        //e.g $mainField = 12345678 
        $substr_1 = substr($mainField,0,3); // 1,3 = 123 
        $substr_2 = substr($mainField,4,4); // 5,4 = 567
        
        if($this->validateCharacters($substr_1) AND $this->validateCharacters($substr_2)){
            return true;
        }

        return false;
    }


}
