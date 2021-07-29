<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode713 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {   
        dd($this->validateCharacter($mainField));
        try {
            if (
                $this->validateLength($mainField)
                // $this->validateCharacter($mainField)
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
        if ($length < 2 || $length > 16 || empty($mainField) ) {
            return false;
        }
        return true;
    }

    private function validateCharacter($mainField) {
        $valid_character = strtoupper('abcdefghijklmnopqrstuvwxyz0123456789');

        if (substr($mainField, 0, 3) === "NBC" ||
            substr($mainField, 0, 3) === "WWV" ||
            substr($mainField, 0, 3) === "WWR")
            $count = 3;
        else if (substr($mainField, 0, 4) === "ECWW") 
            $count = 4;
        else
            return false;
        
        while ($count <= strlen($mainField) - 1) {
            if (strpos($valid_character, substr($mainField, $count, 1)) === false) {
                return false;
            }
            $count++;
        }
        return true;
    }
}