<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode49 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField)  && 
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
        
        return $length == 9 ? true : false;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }

    private function validateCheckDigit($mainField)
    {
        $accountNumber = str_split(substr($mainField, 0, 8));
        $checkDigit = substr($mainField, -1);
    
        $evenPosition = $this->computeEvenOdd($accountNumber, 0);
        $multiply = $evenPosition * 3;
        $oddPosition = $this->computeEvenOdd($accountNumber, 1);
        $sum = $multiply + $oddPosition;

        $remainder = fmod($sum, 10);

        return $checkDigit == $remainder;
    }

    private function computeEvenOdd($splitNumber, $evenOdd) {
        $sum = 0;
        foreach ($splitNumber as $key => $value) {
            if ($key % 2 == $evenOdd) {
                $sum += $value;
            }
        }
        return $sum;
    }
}
