<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode119 implements BillerCdvInterface
{
    CONST WEIGHT = [9, 8, 7, 6, 5, 4];

    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) &&
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
        
        return $length == 10 ? true : false;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }

    private function validateCheckDigit($mainField)
    {
        $accountNumber = str_split(substr($mainField, 3, 6));
        $checkDigit = substr($mainField, -1);
        
        $sum = 0;
        foreach ($accountNumber AS $key => $value) {
            $product = $value * Self::WEIGHT[$key];
            $sum += $product;
        }

        $remainder = fmod($sum, 11);
        $computed = 11 - $remainder;
        if ($remainder == 0) $computed = 0;
        else if ($remainder == 1) return false;
        else if ($remainder == 10) $computed = 1;
    
        return $checkDigit == $computed;
    }
}
