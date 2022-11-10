<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

// Security Bank Mastercard
class BillerCode908 implements BillerCdvInterface {
    const WEIGHT_F = 313131;
    const WEIGHT_S = 3131313;
    private $mainField;
    
    public function validate($mainField, $amount): bool
    {
        $this->mainField = $mainField;
        try {
            return $this->validateField();

        } catch (Throwable $th) {
            throw $th;
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateField()
    {
        
        if (!in_array(strlen($this->mainField), range(9,20))) {
            return false;
        }

        return $this->checkDigit();
    }

    private function checkDigit()
    {
        $mainfield = str_replace('-', '', $this->mainField);
        return !preg_match('%[^A-Za-z0-9]%', $mainfield);
    }
}