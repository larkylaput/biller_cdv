<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode87 implements BillerCdvInterface
{
    const MASTERCARD = [
        530220,
        524260,
        545420,
        612660,
        552829
    ];

    CONST WEIGHT = [2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2];

    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateCharacters($mainField) AND
                $this->validateLength($mainField) AND
                $this->validateBIN($mainField) AND 
                $this->validateCheckDigit($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }

    private function validateLength($mainField)
    {
        return strlen($mainField) <> 16 ? false : true;
    }

    private function validateBIN($mainField)
    {
        if($mainField >= 4404000000000000 AND $mainField <= 4404999999999999){
            return true;
        }
        else if($mainField >= 6126000000000000 AND $mainField <= 6126999999999999){
            return true;
        }
        else if($mainField >= 4622670000000000 AND $mainField <= 4622679999999999){
            return true;
        }
        else{
            $first6 = substr($mainField, 0, 6);
            if (in_array($first6, self::MASTERCARD)){
                return true;
            }
        }

        return false;
    }

    private function validateCheckDigit($mainField)
    {
        $accountNumber = str_split(substr($mainField, 0, 15));
        $checkDigit = substr($mainField, 15, 1);

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
