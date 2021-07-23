<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode93 implements BillerCdvInterface
{
    // PSA
    public function validate($mainField, $amount): bool
    {
        // dd($this->validateLength($mainField));
        try {
            if (
                $this->validateLength($mainField) AND
                $this->validateCharacters($mainField) AND
                $this->validateFormat($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }

        return false;
    }

    public function validateLength($mainField) {
        $mainField = preg_replace('/\D/', '', $mainField);
        return (strlen($mainField) === 10) ? true : false;
    }

    public function validateCharacters($mainField) {
        return (preg_match('/^\d{9}-\d{1}$/', $mainField)) ? true : false;
    }

    public function validateFormat($mainField) {
        $mainField = preg_replace('/\D/', '', $mainField);
        $weight = ['2','3','4','5','6','7','8','9','8'];
        $split = str_split($mainField);

        $last_digit = substr($mainField, -1);

        $multi = [];
        // adding of per number between account number and weight 
        for ($i=0; $i < 9; $i++) { 
            $multi[] = $split[$i] * $weight[$i];
        }

        $sum = array_sum($multi);

        $mod = (int)fmod($sum, 11);

        if ($mod == 0)
            $check_digit = 1;
        else if ($mod == 1)
            $check_digit = 0;
        else
            $check_digit = 11 - $mod;

        return $check_digit == $last_digit;
    }
    
}
