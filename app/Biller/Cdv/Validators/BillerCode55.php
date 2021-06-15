<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode55 implements BillerCdvInterface
{
    CONST WEIGHT = [6, 4, 7, 4, 8, 6, 2, 6, 4, 7, 4, 8, 6];

    public function validate($mainField, $amount): bool
    {
        try {
            // $mainField = preg_replace('/\D/', '', $mainField);
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

        return $length === 14 ? true : false;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }

    private function validateCheckDigit($mainField)
    {
        $accountNumber = str_split(substr($mainField, 0, 13));
        $checkDigit = substr($mainField, 13, 1);

        $formula['Account Number'] = $mainField;
        $formula['Check Digit'] = $checkDigit;

        $product = 0;
        $sum = 0;

        foreach ($accountNumber AS $key => $value) {
            $product = $value * Self::WEIGHT[$key];

            $formula['Product'][] = "$value X ".Self::WEIGHT[$key]. " = $product";
            $formula['Summation'][] = "$sum + $product = " . ($sum + $product);

            $sum += $product;
        }

        $remainder = fmod($sum, 11);
        $computed = 11 - $remainder;
        
        if ($remainder == 1) {
            $computed = 0;
        }

        $formula['Check'][] = "Modulo: $sum % 11 = $remainder";
        $formula['Check'][] = "Checker: 11 - $remainder = $computed";
        $formula['Check'][] = $checkDigit==$computed;

        // dd($formula);
        return $checkDigit == $computed;
    }
}