<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1074 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // PNB Credit Cards
            // $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) and
                $this->validateNumeric($mainField) and
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

    private function validateNumeric($mainField)
    {
        $first6_digits = substr($mainField,0,6);
        return is_numeric($first6_digits);
    }


    private function validateCharacters($mainField){
        $chararacter_code = substr($mainField,6,2);
        if($chararacter_code == 'CP'){
            return true;
        }
        return false;
    }

}
