<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1003 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        // dd($this->validateCharacters($mainField));
        try {
            if (
                $this->validateCharacters($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateCharacters($mainField) {
        return ($mainField == 'PNBSEC224') ? true : false;
    }
}