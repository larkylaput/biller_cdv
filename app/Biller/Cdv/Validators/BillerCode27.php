<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode27 implements BillerCdvInterface
{
    CONST WEIGHT = [15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1];

    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) AND 
                $this->validateCharacters($mainField)
            ) {
                if($this->validateCheckDigit($mainField)){
                    return true;
                }
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField)
    {
        $length = strlen($mainField);
        
        return $length == 16 ? true : false;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }

    private function validateCheckDigit($mainField)
    {
        $accountNumber = str_split(substr($mainField, 0, 15));
        $checkDigit = substr($mainField, -1);
        
        $product = 0;
        $sum = 0;

        foreach ($accountNumber AS $key => $value) {
            $product = $value * Self::WEIGHT[$key];
            $sum += $product;

        }

        $remainder = fmod($sum, 11);
        if ($remainder == 0) $computed = 0;
        else if ($remainder == 1) return false;
        else if ($remainder > 1) $computed = 11 - $remainder;
        
        return $checkDigit == $computed;
    }
}
