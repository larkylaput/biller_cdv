<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1007 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        $mainField = strtoupper($mainField);
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
        return (strlen($mainField) === 7) ? true : false;
    }

    private function validateFormat($mainField) {
        if (substr($mainField, 0, 1) == 'H') {
            if (ctype_alpha(substr($mainField, 1, 1))) {
                if (substr($mainField, 2, 1) == 'B') {
                    if (ctype_alpha(substr($mainField, 3, 1))) {
                        if (is_numeric(substr($mainField, 4, 3))) {
                            return true;
                        }
                    }
                }
            }
        }
        

        return false;
    }
}
