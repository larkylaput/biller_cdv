<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1118 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateFormat($mainField, $amount)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateFormat($mainField, $amount)
    {

        if (strlen($mainField) == 10) {

            if (substr($mainField, 1, 1) == 2 && preg_match('%[^0-9]%', $mainField)) {
                return $amount = 0;
            } else {
                return $amount = 1;
            }
            error_log($amount);
        } else {
            return $amount = 1;
        }
    }
}
