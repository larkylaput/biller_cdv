<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1027 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) &&
                $this->validateFirstDigit($mainField) &&
                $this->validateCharacters($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) === 10) ? true : false;
    }
    
    private function validateFirstDigit ($mainField) {
        return substr($mainField, 0, 1) == '2';
    }

    private function validateCharacters($mainField) {
        return is_numeric(substr($mainField, 1, 9));
    }
}
