<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode46 implements BillerCdvInterface
{
   public function validate($mainField, $amount): bool
    {
        try {
            // $mainField = preg_replace('/\D/', '', $mainField);
			// Eastern Telecom
            if (
                $this->validateLength($mainField) and 
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
        if ($length != 1 and $length != 11) {
            return true;
        }
        return false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }  
}

