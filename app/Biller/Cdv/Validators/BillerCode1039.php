<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1039 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            return true;
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }
}
