<?php

namespace App\Biller\Cdv\Validators;

use Throwable;
use Carbon\Carbon;
use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1105 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        
        try {
            if(
                $this->validateLength($mainField) and 
                $this->validateCharacters($mainField)
            ) {
                return true;
            }

        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateLength($mainField)
    {
        $length = strlen($mainField);
        if ($length > 30 || $length < 1) {
            return false;
        }
        return true;
    }

    private function validateCharacters($mainField)
    {
        // alphanumeric
        return !preg_match('%[^a-zA-Z0-9]%', $mainField);;
    }
}
