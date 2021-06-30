<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1014 implements BillerCdvInterface
{
    CONST WEIGHT = [64, 32, 16, 8, 4, 2, 1];
// Prulife UK
    public function validate($mainField, $amount): bool
    {
        try {
            // $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) AND 
                $this->validateCharacters($mainField) AND 
                $this->validateFormat($mainField) 
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
        $mainField = preg_replace('/\D/', '', $mainField);
        $length = strlen($mainField);
        
        return $length === 8 ? true : false;
    }

    private function validateCharacters($mainField)
    {
        $mainField = preg_replace('/\D/', '', $mainField);
        return is_numeric($mainField);
    }

    private function validateFormat($mainField)
    {
        $first7 = substr($mainField, 0, 7);
        $hypen = substr($mainField, 7, 1);
        $lastDigit = substr($mainField, 8, 1);

        if(is_numeric($first7)){
            if($hypen == "-"){
                if(is_numeric($lastDigit)){
                    return true;
                }
            }
        }
        return false;
    }

    private function validateCheckDigit($mainField)
    {
        $accountNumber = str_split(substr($mainField, 0, 7));
        $checkDigit = substr($mainField, 8, 1);

        $formula['Account Number'] = $mainField;
        $formula['Check Digit'] = $checkDigit;
        
        $product = 0;
        $sum = 0;

        foreach ($accountNumber AS $key => $value) {
            $product = $value * Self::WEIGHT[$key];

            $formula['Product'][] = "$value X ".Self::WEIGHT[$key]. " = $product";

            if($product > 8){
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
        $remainder = fmod($sum, 11);
        $computed = 11 - $remainder;

        if ($remainder == 0) {
            $computed = 0;
        }
        
        $formula['Check'][] = "Modulo: $sum % 11 = $remainder";
        $formula['Check'][] = "Checker: 11 - $remainder = $computed";
        $formula['Check'][] = $checkDigit==$computed;

        // dd($formula);
        
        return $checkDigit == $computed;
    }
}