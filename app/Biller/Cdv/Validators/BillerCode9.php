<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode9 implements BillerCdvInterface
{
    const WEIGHT1 = [1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2];
    const WEIGHT2 = [1, 1, 2, 1, 1, 2, 1, 1, 2];

    public function validate($mainField, $amount): bool
    {
        try {
            $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
                $this->validateFormat($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        $length = strlen($mainField);
        return ($length === 13 || $length === 10) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateFormat($mainField)
    {
        $countLen = 9;
        $weight = Self::WEIGHT2;
        if (strlen($mainField) === 13) {
            $countLen = 12;
            $weight = Self::WEIGHT1;
        }

        $accountNumber = str_split(substr($mainField, 0, $countLen));
        $checkDigit = substr($mainField, -1);

        $formula['Account Number'] = $mainField;
        $formula['Check Digit'] = $checkDigit;

        $product = 0;
        $sum = 0;

        foreach ($accountNumber AS $key => $value) {
            $product = $value * $weight[$key];

            $formula['Product'][] = "$value X ". $weight[$key]. " = $product";

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

        if ($remainder == 0) {
            $computed = 0;
        }
        
        $formula['Check'][] = "Modulo: $sum % 10 = $remainder";
        $formula['Check'][] = "Checker: 10 - $remainder = $computed";
        $formula['Check'][] = $checkDigit==$computed;

        // dd($formula);
        
        return $checkDigit == $computed;
    }
}
