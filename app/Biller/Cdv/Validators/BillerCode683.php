<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode683 implements BillerCdvInterface
{
    CONST LETTER = ['A', 'B', 'C', 'D', 'F', 'G', 'H', 'I', 'M', 'S', 'Z'];

    public function validate($mainField, $amount): bool
    {
        // dd($this->validateFormat($mainField));
        try {
            if(
                $this->validateLength($mainField) AND
                $this->validateFormat($mainField)
            ) {
                return true;
            }

        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateLength($mainField)
    {
        return (strlen($mainField) === 6) ? true : false;
    }

    private function validateFormat($mainField) {
        $first = substr($mainField, 0, 1);
        $second = substr($mainField, 1, 1);

        if($second == 1 OR $second == 2){
            if (in_array($first, self::LETTER)){
                return is_numeric(substr($mainField, 2));
            }
        }

        return false;
    }
}