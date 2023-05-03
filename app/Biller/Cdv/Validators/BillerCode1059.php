<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1059 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) &&
                $this->validateLetter($mainField) &&
                $this->validateNumber($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) == 9) ? true : false;
    }

    private function validateLetter($mainField) {
        return (substr($mainField, 0, 6) == 'SSDSDA') ? true : false;
    }

    private function validateNumber($mainField) {
        $check = substr($mainField, 6, 3);
        $numbers = ['001','002','003','004','005'];

        return in_array($check, $numbers);
    }
}
