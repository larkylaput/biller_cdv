<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode72 implements BillerCdvInterface
{
    CONST WEIGHT = [4, 3, 2, 7, 6, 5, 4 ,3 ,2];
    CONST BIN = ['0150', '0200', '0350', '0370', '0400', '0500', '0600', '0700', '0800', '0900', '0950', '0720', '0730', '0960', '0450', '0550', '0650', '0750', '0850', '0870'];

    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) AND 
                $this->validateCharacters($mainField) AND 
                $this->validateBIN($mainField)
            ) {
                if($this->validateCheckDigit($mainField)){
                    return true;
                }
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField)
    {
        return (strlen($mainField) == 14) ? true : false;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }

    private function validateBIN($mainField)
    {
        $first4 = substr($mainField, 0, 4);
        return in_array($first4, self::BIN);
    }

    private function validateCheckDigit($mainField)
    {
        $accountNumber = str_split(substr($mainField, 4, 9));
        $checkDigit = substr($mainField, -1);
        
        $product = 0;
        $sum = 0;

        foreach ($accountNumber AS $key => $value) {
            $product = $value * Self::WEIGHT[$key];
            $sum += $product;
        }

        $diff = fmod($sum, 11);
        $computed = 11 - $diff;

        return $checkDigit == $computed;
    }
}
