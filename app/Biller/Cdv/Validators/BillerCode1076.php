<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1076 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
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
        return (strlen($mainField) === 18) ? true : false;
    }

    private function validateCharacters($mainField) {
        return (substr($mainField, 0, 4) === '8000' && is_numeric($mainField)) ? true : false;
    } 

    private function validateFormat($mainField) {
        $counter = 0;
        $total = 0;

        while ($counter <= 16) {
            $total += (int)substr($mainField, $counter, 1);
            $counter++;
        }

        $last_digit_mainField = substr($mainField, -1);
        $last_digit_total = substr($total, strlen($total) - 1, 1);

        return $last_digit_total === $last_digit_mainField;
    }
}
