<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1054 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) and
                $this->validateFirstFourDigits($mainField) and 
                $this->validateFifthDigits($mainField) and
                $this->validateLastFourDigits($mainField)
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
        return $length === 9 ? true : false;
    }

    private function validateFirstFourDigits($mainField)
    {
        $firstFourDigits = substr($mainField, 0, 4);
        if (intval($firstFourDigits) < 2009 or intval($firstFourDigits) > 2030) {
            return false;
        }
        return true;
    }

    private function validateFifthDigits($mainField)
    {
        $digit = substr($mainField, 4, 1);
        return $digit === '-' ? true : false;
    }

    private function validateLastFourDigits($mainField)
    {
        return is_numeric( substr($mainField, 5, 4));
    }
}
