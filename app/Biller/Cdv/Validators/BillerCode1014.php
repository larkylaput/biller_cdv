<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1014 implements BillerCdvInterface
{
    CONST WEIGHT = [64, 32, 16, 8, 4, 2, 1];
    CONST FIRST_DIGIT = [1, 6, 8, 9];
    // Prulife UK
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
        $length = strlen(preg_replace('/\D/', '', $mainField));
        if($length == 8){
            if($this->validateCharacters($mainField)){
                if($this->validateRange($mainField)){
                    return true;
                }
                else{
                    return $this->validateCheckDigit($mainField);
                }
            }
        }
        if($length == 13){
            if($this->validateFormat($mainField)){
                return true;
            }
        }

        return false;
    }

    private function validateCharacters($mainField)
    {
        $mainField = preg_replace('/\D/', '', $mainField);
        return is_numeric($mainField);
    }

    private function validateFormat($mainField)
    {
        $firstChar = substr($mainField, 0, 1);
        $remainingChar = substr($mainField, 1, 13);

        if(is_numeric($firstChar)){
            return true;
        }

        if(!preg_match("/^[aA-zZ0-9]+$/", $remainingChar)){
            return true;
        };

        return false;
    }

    private function validateRange($mainField)
    {
        $firstDigit = substr($mainField, 0, 1);
        if(in_array($firstDigit, SELF::FIRST_DIGIT)){
            return true;
        }

        if($mainField >= 1 AND $mainField <= 71000){
            return true;
        }

        return false;
    }

    private function validateCheckDigit($mainField)
    {
        $accountNumber = str_split(substr($mainField, 0, 7));
        $checkDigit = substr($mainField, 7, 1);

        $formula['Account Number'] = $mainField;
        $formula['Check Digit'] = $checkDigit;
        
        $product = 0;
        $sum = 0;

        foreach ($accountNumber AS $key => $value) {
            $product = $value * Self::WEIGHT[$key];

            $formula['Product'][] = "$value X ".Self::WEIGHT[$key]. " = $product";
            $formula['Summation'][] = "($product) $sum + $product = " . ($sum + $product);

            $sum += $product;
        }
        $remainder = fmod($sum, 11);
        $computed = 11 - $remainder;

        if($computed == 10){
            $computed = 9;
        }
        if($computed == 11){
            $computed = 1;
        }
        
        $formula['Check'][] = "Modulo: $sum % 11 = $remainder";
        $formula['Check'][] = "Checker: 11 - $remainder = $computed";
        $formula['Check'][] = $checkDigit==$computed;

        // dd($formula);
        
        return $checkDigit == $computed;
    }
}