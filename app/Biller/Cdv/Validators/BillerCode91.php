<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode91 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        // dd($this->validateFormat($mainField));
        try {
            if($this->validateLength($mainField) AND
                $this->validateCharacter($mainField) AND
                $this->validateFormat($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    public function validateLength($mainField) {
        return (strlen($mainField) === 7) ? true : false;
    }

    public function validateCharacter($mainField) {
        return is_numeric($mainField);
    }

    public function validateFormat($mainField) {
        $check_digit = (int)substr($mainField, 6, 1);
        $first_count = 0;
        $second_count = 7;
        $first_sum = 0;
        $second_sum = 0;

        $formula['Account Number'] = $mainField;
        $formula['Check Digit'] = $check_digit;

        while ($first_count <= 5) {
            $first_sum = (int)substr($mainField, $first_count, 1);
            $product = $first_sum * $second_count;

            $formula['Product'][] = "$first_sum * $second_count = $product";
            $formula['Summation'][] = "($product) $second_sum + $product = " . ($second_sum + $product);

            $second_sum += $product;

            $first_count++;
            $second_count--;
        }

        $mod = (int)fmod($second_sum, 11);
        $computed = 11 - $mod;

        $formula['Check'][] = "Modulo: $second_sum % 11 = $mod";
        $formula['Check'][] = "Checker: 11 - $mod = $computed";
        $formula['Check'][] = $check_digit===$computed;

        // return $formula;

        return $check_digit === $computed;
    }
}
