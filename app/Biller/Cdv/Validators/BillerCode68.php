<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode68 implements BillerCdvInterface
{
    CONST WEIGHT = [2, 1, 2, 1, 2, 1, 2, 1, 2, 1];

    public function validate($mainField, $amount): bool
    {
        try {
            if(
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
                $this->validateCheckDigit($mainField)
            ) {
                return true;
            }

        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) == 11) ? true : false; 
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateCheckDigit($mainField)
    {
        $accountNumber = str_split(substr($mainField, 0, 10));
        $checkDigit = substr($mainField, -1);

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
        $diff = fmod($sum, 10);
        $nextHighNum = ($sum - $diff) + 10;
        $computed = $nextHighNum - $sum;
        if ($diff == 0) {
            $computed = 0;
        }
        
        $formula['Check'][] = "Next Higher Number: ceil($sum) = $nextHighNum";
        $formula['Check'][] = "Checker: $nextHighNum - $sum = $computed";
        $formula['Check'][] = $checkDigit==$computed;

        // dd($formula);
        
        return $checkDigit == $computed;
    }
   
}
