<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1057 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) AND 
                $this->validateCharacters($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField)
    {
        $length = strlen($mainField);
        
        return $length === 4 ? true : false;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }
}
