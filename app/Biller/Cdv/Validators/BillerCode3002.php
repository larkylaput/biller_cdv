<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode3002 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        $mainField = strtoupper($mainField);

        try {
            if (
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
                $this->validateAmount($amount)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) === 4) ? true : false;
    } 

    private function validateCharacters($mainField) {
        return ctype_alnum($mainField);
    }

    private function validateAmount($amount) {
        $validAmount = [3000,5000,5500,10000];
        return in_array($amount, $validAmount);
    }
}
