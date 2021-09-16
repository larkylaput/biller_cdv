<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1034 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            return $this->validateLength($mainField) && $this->validateCharacters($mainField);
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }

        return true;
    }

    private function validateLength($mainField) {
        $length = strlen($mainField);
        return ($length >= 8 && $length <= 20) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }
}
