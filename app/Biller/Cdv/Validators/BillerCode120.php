<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode120 implements BillerCdvInterface
{
    CONST WEIGHT = [2, 9, 8, 7, 6, 5, 4, 3, 2];

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
        return (strlen($mainField) == 10) ? true : false;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }

    private function validateCheckDigit($mainField)
    {
        $accountNumber = str_split(substr($mainField, 0, 9));
        $checkDigit = substr($mainField, -1);
        
        $product = 0;
        $sum = 0;

        foreach ($accountNumber AS $key => $value) {
            $product = $value * Self::WEIGHT[$key];
            $sum += $product;
        }

        $diff = fmod($sum, 11);
        dd($diff);
        $computed = 11 - $diff;
        if ($diff == 1) $computed = 0;
        else if ($diff == 0) $computed = 1;

        return $checkDigit == $computed;
    }
}
