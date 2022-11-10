<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode110 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        // dd($this->validateFormat($mainField));
        try {
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
        return (strlen($mainField) === 10) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateFormat($mainField) {
        $check_digit = (int)substr($mainField, strlen($mainField) - 1, 1);
        $index = 2;
        $sum = 0;
        $product = 0;
        $remainder = 0;
        $computed = 0;

        $formula['Account Number'] = $mainField;
        $formula['Check Digit'] = $check_digit;

        while ($index <= strlen($mainField) - 2) {
            $mainField_substring = (int)substr($mainField, $index, 1);

            $multi = 2 - fmod($index, 2);
            $product = $mainField_substring * $multi;

            $formula['Product'][] = "$mainField_substring X $multi = $product";

            if ($product > 9) {
                $product1 = (int)substr($product, 0, 1);
                $product2 = (int)substr($product, 1, 1);

                $formula['Product'][] = "($product) $product1 + $product2 = ". ($product1 + $product2);
                $product = $product1 + $product2;
            }

            $formula['Summation'][] = "($product) $sum + $product = " . ($sum + $product);

            $sum += $product;

            $index++;
        }

        $remainder = (int)fmod($sum, 10);

        if ($remainder === 0)
            $computed = 0;
        else
            $computed = 10 - $remainder;

        if ($computed > 10)
            $computed = 0;

        $formula['Check'][] = "Modulo: $sum % 10 = $remainder";
        $formula['Check'][] = "Checker: 10 - $remainder = $computed";
        $formula['Check'][] = $check_digit===$computed;

        // return $formula;

        return $computed === $check_digit;
    }
}
