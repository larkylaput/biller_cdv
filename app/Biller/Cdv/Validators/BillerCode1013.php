<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1013 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        // dd($this->validateCharacter($mainField));
        try {
            // $mainField = preg_replace('/\D/', '', $mainField);
            if ($this->validateLength($mainField) &&
                $this->validateCharacter($mainField) 
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        $length = strlen($mainField);
        return ($length >= 1 && $length <= 30) ? true : false;
    }

    private function validateCharacter ($mainField) {
        
        $regex = preg_match('/[\'!^£$%&*()}{@#~?><>,|=_+¬-]/', $mainField);
        if($regex)
        {
            return false;
        }
        else
        {
            return true;
        }

    }


}
