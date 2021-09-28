<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1012 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if ($this->validateLength($mainField) &&
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
        return (strlen($mainField) === 13) ? true : false;
    }

    private function validateCharacters ($mainField) {
        return is_numeric($mainField);
    }
}
