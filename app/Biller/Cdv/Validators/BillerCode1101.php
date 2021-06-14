<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1101 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {

        try {
            // PNB Credit Cards
            $mainField = preg_replace('/\D/', '', $mainField); // remove all the non numberic characters
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
        if ($length <> 8) {
            return false;
        }
        return true;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }
}
