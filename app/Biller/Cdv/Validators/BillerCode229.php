<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode229 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if($this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
                $this->validateFormat($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateLength($mainField) {
        return strlen($mainField) === 13 ? true : false;
    }
    
    private function validateCharacters($mainField) {
        return ctype_alnum($mainField);
    }

    private function validateFormat($mainField) {
        $first = substr($mainField, 0, 3);
        $second = substr($mainField, 3, 10);

        return (ctype_alpha($first) && is_numeric($second)) ? true : false;
    }
}
