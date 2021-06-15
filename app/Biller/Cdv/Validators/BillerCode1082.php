<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1082 implements BillerCdvInterface
{
    CONST FORMAT = [2, 7];

    public function validate($mainField, $amount): bool
    {
        try {
            $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) and 
                $this->validateFirstTwoDigits($mainField)
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
        
        return $length === 8 ? true : false;
    }

    private function validateFirstTwoDigits($mainField) {
        $firstTwo = substr($mainField, 0, 2);

        if(($firstTwo >= 00 AND $firstTwo <= 40) OR ($firstTwo >= 90 AND $firstTwo <= 99)){
            return true;
        }
        return false;
    }
}

