<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode7 implements BillerCdvInterface
{
    CONST WEIGHT = [6,5,4,3,2,1];

    public function validate($mainField, $amount): bool
    {
        try {
            $mainField = preg_replace('/\D/', '', $mainField);
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
        
        return $length === 7 ? true : false;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }

    private function validateCheckDigit($mainField)
    {
        $accountNumber = str_split(substr($mainField, 0, 6));
        $checkDigit = substr($mainField, 6, 1);

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
        
        $formula['Check'][] = "Modulo: $sum % 10 = $remainder";
        $formula['Check'][] = "Checker: 10 - $remainder = $computed";
        $formula['Check'][] = $checkDigit==$computed;

        if ($remainder === 10) {
            $computed = 0;
        }
        
        return $checkDigit == $computed;
    }
}
