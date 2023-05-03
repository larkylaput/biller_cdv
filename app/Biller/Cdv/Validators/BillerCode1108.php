<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1108 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if(
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField)
            ) {
                return true;
            }

        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateLength($mainField) {
        $length = strlen($mainField);
        return ($length >= 8 && $length <= 12) ? true : false;
    }

    private function validateCharacters($mainField) {
        if (is_numeric(substr($mainField, 0, 6))) {
            if (ctype_alnum(substr($mainField, 6, 6))) {
                return true;
            }
        }
        
        return false;
    }

}
