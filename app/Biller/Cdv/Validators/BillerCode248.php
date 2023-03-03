<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode248 implements BillerCdvInterface
{
    const CODE = ['248','267','264','263','265'];
    
    // One Archers Place Condominium Corporation
    public function validate($mainField, $amount): bool
    {
        // 248021064500
        // 248023019307
        // 248023064203
        // 248023033309
        // 248023066906

        // dd($this->validateFormat($mainField));
        try {
            if($this->validateLength($mainField) AND
                $this->validateCharacters($mainField) AND
                $this->validateFirst3Digits($mainField) AND
                $this->validateCheckDigit($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    public function validateLength($mainField) {
        return strlen($mainField) === 12 ? true : false;
    }
    
    public function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    public function validateFirst3Digits($mainField) {
        $first3digit = substr($mainField, 0, 3);
        $result = false;
        
        if (in_array($first3digit, self::CODE)){
            return true;
        }

        return $result;
    }

    private function validateCheckDigit($mainField)
    {
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
