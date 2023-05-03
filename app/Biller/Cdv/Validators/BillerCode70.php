<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode70 implements BillerCdvInterface
{
    CONST WEIGHT1 = [9, 8, 7, 6, 5, 4, 3, 2, 1];
    CONST WEIGHT2 = [2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2];

    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) AND 
                $this->validateCharacters($mainField) AND
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
        return (strlen($mainField) === 16) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateFormat($mainField) {
        $first9 = substr($mainField, 0, 9);
        $first6 = substr($mainField, 0, 6);

        $checkNumber = [140651991, 140651992, 140651993, 140651994, 140651995];

        $result = false;
        if (in_array($first9, $checkNumber)) {
            $result = $this->validateCheckDigit($mainField, 'local');
        } else if ($first6 == '526737') {
            $result = $this->validateCheckDigit($mainField, 'international');
        }

        return $result;
    }

    private function validateCheckDigit($mainField, $check)
    {

        $accountNumber = str_split(substr($mainField, 0, 15));
        $weight = Self::WEIGHT2;
        if ($check == 'local') {
            $accountNumber = str_split(substr($mainField, 6, 9));
            $weight = Self::WEIGHT1;
        }

        $checkDigit = substr($mainField, -1);

        $formula['Account Number'] = $mainField;
        $formula['Usage'] = $accountNumber;
        $formula['Check Digit'] = $checkDigit;

        
        $product = 0;
        $sum = 0;
        foreach ($accountNumber AS $key => $value) {
            $product = $value * $weight[$key];

            $formula['Product'][] = "$value X ". $weight[$key]. " = $product";

            if ($check == 'international') {
                if($product > 9){
                    $result = str_split($product);
                    foreach ($result AS $i => $val) {
                        $formula['Summation'][] = "($product) $sum + $val = " . ($sum + $val);
    
                        $sum += $val;
                    }
                } else {
                    $formula['Summation'][] = "($product) $sum + $product = " . ($sum + $product);
                    $sum += $product;
                }
            } else {
                $formula['Summation'][] = "($product) $sum + $product = " . ($sum + $product);
                $sum += $product;
            }
        }

        if ($check == 'local') {
            $remainder = fmod($sum, 11);
            $formula['Check'][] = "Modulo: $sum % 10 = $remainder";
        } else {
            $remainder = fmod($sum, 10);
            $formula['Check'][] = "Modulo: $sum % 10 = $remainder";

            $formula['Check'][] = "10 - $remainder = " . (10 - $remainder);
            $remainder = 10 - $remainder;
        }

        if ($remainder == 10) {
            $remainder = 0;
        }

        $formula['Check'][] = $checkDigit==$remainder;
        
        // dd($formula);
        
        return $checkDigit == $remainder;
    }
}
