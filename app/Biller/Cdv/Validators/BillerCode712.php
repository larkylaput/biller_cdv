<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode712 implements BillerCdvInterface
{

    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) && 
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
        $length = strlen($mainField);
        return ($length >= 1 && $length <= 30) ? true : false;
    }

    private function validateCharacters($mainField) {
        return ctype_alnum($mainField);
    }
}
