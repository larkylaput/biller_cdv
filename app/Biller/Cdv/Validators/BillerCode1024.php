<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1024 implements BillerCdvInterface
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
        
        if(strlen($mainField) > 9 || strlen($mainField) < 5){
            return false;
        }
        
        return true;
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
