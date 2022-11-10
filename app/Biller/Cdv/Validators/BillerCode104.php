<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode104 implements BillerCdvInterface
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
        return (strlen($mainField) === 8) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateFormat($mainField) {
        $weight = [2,4,6,8,3,5,7];
        $last_digit = substr($mainField, -1);
        $accountNumber = str_split(substr($mainField, 0, 7));

        $formula['Account Number'] = $mainField;
        $formula['Split Number'] = $accountNumber;
        $formula['Last Digit'] = $last_digit;

        $product = 0;
        $sum = 0;

        foreach ($accountNumber as $key => $value) {
            $multi1 = $accountNumber[$key];
            $multi2 = $weight[$key];

            $product = $multi1 * $multi2;
            $formula['Multiply'][] = "$multi1 * $multi2 = $product";

            $formula['Addition'][] = "$sum + $product = " . ($sum + $product);
            $sum += $product;
        }

        $modulo = fmod($sum, 11);
        $formula['Module'] = "$sum % 11 = $modulo"; 

        $computed = 11 - $modulo;

        if ($computed == 10) {
            $computed = 0;
        }

        $formula['Subtraction'] = "11 - $modulo = $computed"; 
        $formula['Check Digit'] = $last_digit == $computed; 

        // dd($formula);

        return $last_digit == $computed;
    }
}
