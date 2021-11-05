<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1079 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // PNB Credit Cards
            // $mainField = preg_replace('/\D/', '', $mainField); // remove all the non numberic characters
            if(
                $this->validateLength($mainField)  AND 
                $this->validateCharacters($mainField) 
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
        if(strlen($mainField) > 11 OR strlen($mainField) < 7){
            return false;
        }
        return true;
    }

    private function validateCharacters($mainField) {
        
        if (preg_match('/[\'^£$%&*;:()}{@#~?><>,|=_+¬-]/', $mainField))
        {
          return false;
        }
        return true;
    }
}
