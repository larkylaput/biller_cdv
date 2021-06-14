<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode100 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // PNB Credit Cards
            $mainField = preg_replace('/\D/', '', $mainField);
            if ($amount < 10) {
                return false;
            }
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }

        return true;
    }
}
