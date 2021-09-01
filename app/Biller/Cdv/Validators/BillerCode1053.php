<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1053 implements BillerCdvInterface
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
        
        if($length < 9 ) {
            return false;
        }else if($length > 15){
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
