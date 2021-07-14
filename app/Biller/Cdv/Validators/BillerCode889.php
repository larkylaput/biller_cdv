<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode889 implements BillerCdvInterface
{
    CONST WEIGHT = [1, 2, 1, 2, 1, 2, 1, 2, 1, 2];

    public function validate($mainField, $amount): bool
    {
        try {
            if($this->validateField($mainField)){
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateField($mainField)
    {
        $length = strlen($mainField);
        if($length == 11){
            if($this->validateCharacters($mainField)){
                return $this->validateCheckDigit($mainField);
            }
        }
        if($length >= 13 AND $length <= 17){
            if($this->validateFormat($mainField)){
                return true;
            }
        }

        return false;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }

    private function validateFormat($mainField)
    {
        return preg_match("/^[aA-zZ0-9]+$/", $mainField);
    }

    private function validateCheckDigit($mainField)
    {
        $accountNumber = str_split(substr($mainField, 0, 10));
        $checkDigit = substr($mainField, 10, 1);

        $formula['Account Number'] = $mainField;
        $formula['Check Digit'] = $checkDigit;
        
        $product = 0;
        $sum = 0;

        foreach ($accountNumber AS $key => $value) {
            $product = $value * Self::WEIGHT[$key];

            $formula['Product'][] = "$value X ".Self::WEIGHT[$key]. " = $product";
            if($product > 9){
                $result = str_split($product);
                foreach ($result AS $i => $val) {
                    $formula['Summation'][] = "($product) $sum + $val = " . ($sum + $val);

                    $sum += $val;
                }
            }
            else{
                $formula['Summation'][] = "($product) $sum + $product = " . ($sum + $product);

                $sum += $product;
            }
        }
        $remainder = fmod($sum, 10);
        $computed = 10 - $remainder;

        if($remainder == 0){
            $computed = 0;
        }
        
        $formula['Check'][] = "Modulo: $sum % 10 = $remainder";
        $formula['Check'][] = "Checker: 10 - $remainder = $computed";
        $formula['Check'][] = $checkDigit==$computed;

        // dd($formula);
        
        return $checkDigit == $computed;
    }
}