<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode694 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        // dd($this->validateFormat($mainField));
        try {
            if(
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
                $this->validateFormat($mainField)
            ) {
                return true;
            }

        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) === 9 || strlen($mainField) === 14) ? true : false; 
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateFormat($mainField) {
        $index = 0;
        $sum = 0;
        $str_len = strlen($mainField) - 1;
        $check_digit = (int)substr($mainField, $str_len, 1);

        while ($index < $str_len) {
            $value = (int)substr($mainField, $index, 1);
            $multi = 2 - (int)fmod($index+1, 2);
            $product = $value * $multi;
            $formula['Product'][] = "$value X $multi = $product";

            if ($product > 9) {
                $first_value = (int)substr($product, 0, 1);
                $second_value = (int)substr($product, 1, 1);
                $val = $first_value + $second_value;

                $formula['Summation'][] = "($product) $sum + $val = " . ($sum + $val);

                $sum += $val;
            } else {
                $formula['Summation'][] = "($product) $sum + $product = " . ($sum + $product);
                $sum += $product;
            }
            
            $index++;
        }

        $remainder = (int)fmod($sum, 10);
        $computed = 10 - $remainder;

        if ($computed === 10) 
            $computed = 0;

        $formula['Check'][] = "Modulo: $sum % 10 = $remainder";
        $formula['Check'][] = "Checker: 10 - $remainder = $computed";
        $formula['Check'][] = $check_digit==$computed;

        // return $formula;

        return $computed === $check_digit;
    } 
}