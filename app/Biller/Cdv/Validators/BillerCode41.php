<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode41 implements BillerCdvInterface
{
    CONST WEIGHT = [6, 4, 7, 8, 6, 7, 5, 4, 3, 2];

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
        return (strlen($mainField) == 11) ? true : false;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }

    private function validateCheckDigit($mainField)
    {
        $accountNumber = str_split(substr($mainField, 0, 10));
        $checkDigit = substr($mainField, -1);
        
        $product = 0;
        $sum = 0;

        foreach ($accountNumber AS $key => $value) {
            $product = $value * Self::WEIGHT[$key];
            $sum += $product;
        }

        $diff = fmod($sum, 11);
        $computed = 11 - $diff;
        if ($computed > 9) $computed = 0;

        return $checkDigit == $computed;
    }
}
