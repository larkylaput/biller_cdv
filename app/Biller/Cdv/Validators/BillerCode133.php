<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode133 implements BillerCdvInterface
{
    CONST WEIGHT = [2, 1, 2, 1, 2, 1, 2, 1, 2];

    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) AND 
                $this->validateCharacters($mainField)            ) {
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
        $accountNumber = str_split(substr($mainField, 0, 9));
        $checkDigit = substr($mainField, -1);
        
        $product = 0;
        $sum = 0;

        foreach ($accountNumber AS $key => $value) {
            $product = $value * Self::WEIGHT[$key];

            if($product > 9){
                $result = str_split($product);
                foreach ($result AS $i => $val) {
                    $sum += $val;
                }
            } else {
                $sum += $product;
            }
        }
        $diff = fmod($sum, 10);
        $computed = 10 - $diff;
        if ($diff == 0) {
            $computed = 0;
        }

        return $checkDigit == $computed;
    }
}
