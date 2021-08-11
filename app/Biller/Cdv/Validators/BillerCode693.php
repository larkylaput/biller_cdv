<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode693 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        // dd($this->validateFormat($mainField));
        try {
            if(
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
                $this->validateFirstAndSecondDigit($mainField) &&
                $this->validateFormat($mainField)
            ) {
                return true;
            }

        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateLength($mainField)
    {
        return (strlen($mainField) === 9) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateFirstAndSecondDigit($mainField) {
        return (substr($mainField, 0, 1) !== '0' && substr($mainField, 0, 1) !== '1') ? false : true;
    }

    private function validateFormat($mainField) {
        $weight = '86423597';
        $string_head = substr($mainField, 0, 8);
        $total = 0;
        $index = 0;
        $amount = 0;
        $check_digit = (int)substr($mainField, 8, 1);

        $formula['Account Number'] = $mainField;
        $formula['Check Digit'] = $check_digit;

        while ($index <= 7) {
            $first_number = (int)substr($string_head, $index, 1);
            $second_number = (int)substr($weight, $index, 1);

            $amount = $first_number * $second_number;

            $formula['Product'][] = "$first_number X $second_number = $amount";
            $formula['Summation'][] = "($amount) $total + $amount = " . ($total + $amount);

            $total += $amount;

            $index++;
        }

        $remainder =  (int)fmod($total, 11);
        $computed = 11 - $remainder;

        if ($remainder === 0)
            $computed = 5;
        else if ($remainder === 1)
            $computed = 0;

        $formula['Check'][] = "Modulo: $total % 11 = $remainder";
        $formula['Check'][] = "Checker: 11 - $remainder = $computed";
        $formula['Check'][] = $computed === $check_digit;

        // return $formula;

        return $computed === $check_digit;
    }
}