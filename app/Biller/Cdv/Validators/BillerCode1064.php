<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1064 implements BillerCdvInterface
{
    // 266120055009  
    // 266120069405 
    // 266120034103 
    // 266120020403
    // 266120000906

    public function validate($mainField, $amount): bool
    {
        
        try {
            if (
                $this->validateLength($mainField) AND
                $this->validateFirst3Digit($mainField) AND
                $this->validateCharacters($mainField) AND
                $this->validateFormat($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }

        return false;
    }

    public function validateLength($mailField) {
        return (strlen($mailField) === 12) ? true : false;
    }

    public function validateFirst3Digit($mailField) {
        return (substr($mailField, 0, 3) === '266') ? true : false;
    }

    public function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    public function validateFormat($mainField) {

        $accountNumber = str_split(substr($mainField, 0, 10));
        $checkDigit = substr($mainField, -2);

        $formula['Account Number'] = $mainField;
        $formula['Check Digit'] = $checkDigit;
        
        $product = 0;
        $sum = 0;

        foreach ($accountNumber AS $key => $value) {
            $index = $key + 1;
            $multi = 2 - fmod($index, 2);
            $product = $value * $multi;

            $formula['Product'][] = "$value X ".$multi. " = $product";

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
