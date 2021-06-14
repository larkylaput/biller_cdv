<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1087 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // PNB Credit Cards
            $mainField = preg_replace('/\D/', '', $mainField); // remove all the non numberic characters
            if(
                $this->validateLength($mainField)  and 
                $this->validateCharacters($mainField) and
                $this->validateDigits($mainField) 
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
        if ($length <> 8) {
            return false;
        }
        return true;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateDigits($mainField){

        $sub_str = substr($mainField,0,4);

        if((int)$sub_str >= 2000){
           return true;
        }

        return false;

    }
}
