<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1034 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            return $this->validateLength($mainField) && 
            $this->validateCharacters($mainField) &&
            $this->validateFormat($mainField);
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }

        return true;
    }

    private function validateLength($mainField) {
        $length = strlen($mainField);
        return ($length >= 8 && $length <= 9) ? true : false;
    }

    private function validateCharacters($mainField) {
        return ctype_alnum($mainField);
    }

    private function validateFormat($mainField) {
        $firstToEightDigit = substr($mainField, 0, 8);
        $nineDigit = substr($mainField, -1);

        return is_numeric($firstToEightDigit) && ctype_alnum($nineDigit) ? true : false;
    }
}
