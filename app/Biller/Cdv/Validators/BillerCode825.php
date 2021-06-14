<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode825 implements BillerCdvInterface
{
    CONST FORMAT = [2, 7];

    public function validate($mainField, $amount): bool
    {
        try {
            $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) and 
                $this->firstFourCharacters($mainField) and 
                $this->validateStudenNumber($mainField)
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
        return strlen($mainField) == 11;
    }

    private function firstFourCharacters($mainField)
    {
        return substr($mainField,0,4) >= 2002;
    }

    private function validateStudenNumber($mainField)
    {
        $pos = 10;
        $sum = 0;

        for ($i = 11; $i <= 20; $i++) {
            $sum += intval(substr($mainField, $pos -1, 1)) * $i;
            $pos -= 1;
        }
        $lastDigit = substr($mainField, -1, 1);

        $remainder = $sum % 10;

        return $remainder == $lastDigit;
    }

    private function validateHypen($digit)
    {
        return $digit === '-' ? true : false;
    }
}