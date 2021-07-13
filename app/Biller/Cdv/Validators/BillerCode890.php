<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode890 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        // dd($this->validateCharacters($mainField));
        try {
            // PNB Credit Cards
            // $mainField = preg_replace('/\D/', '', $mainField); // remove all the non numberic characters
            if(
                $this->validateLength($mainField)  and 
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
        if ($length < 8 and $length > 20) {
            return false;
        }
        return true;
    }

    private function validateCharacters($mainField) {
        if(ctype_alpha($mainField) or is_numeric($mainField)){
            return false;
        }

        return true;
    }

}
