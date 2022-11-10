<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode48 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField)  && 
                $this->validateCharacters($mainField) &&
                $this->validateLastDigit($mainField)
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
        
        return $length == 7 ? true : false;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }

    private function validateLastDigit($mainField) {
        return in_array(substr($mainField, -1), [1,4,7]);
    }

    private function validateCheckDigit($mainField)
    {
        $accountNumber = str_split(substr($mainField, 0, 6));
        $checkDigit = substr($mainField, -1);
    
        $sum = $this->computeSum($accountNumber);

        $multiply = $sum * 3;
        $splitNum = str_split($multiply);

        $finalSum = $this->computeSum($splitNum);

        $remainder = fmod($finalSum, 9);
        $computed = 7 - $remainder;

        return $checkDigit == $computed;
    }

    private function computeSum($splitNumber) {
        $sum = 0;
        foreach ($splitNumber as $value) {
            $sum += $value;
        }

        return $sum;
    }
}
