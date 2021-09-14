<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1115 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) &&
                $this->validateFormat($mainField)
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
        return ($length >= 7 && $length <= 8) ? true : false;
    }

    private function validateFormat($mainField) {
        if (strlen($mainField) == 7) {
            $letters = substr($mainField, 0, 2);
            $number = substr($mainField, 2, 5);   
        } else {
            $letters = substr($mainField, 0, 3);
            $number = substr($mainField, 3, 5);   
        }

        if (ctype_alpha($letters) && ctype_digit($number)) {
            return true;
        }

        return false;
    }

    
}
