<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode254 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        dd(123);
        try {
            // $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) AND 
                $this->validateCharacters($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField)
    {
        $length = strlen($mainField);
        
        return $length <> 15 ? false : true;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }

    private function validateChars($mainField){

        $temp1 = substr($mainField,0,7);
        $temp2 = substr($mainField ,7,2);
        $temp3 = substr($mainField ,9,3);
        $temp4 = substr($mainField ,12,2);
        $temp5 = substr($mainField ,14,1);

        if(!$this->validateCharacters($temp1) OR 
            !$this->validateCharacters($temp2) OR
            ($temp3 != 'JAN' AND
            $temp3 != 'FEB' AND
            $temp3 != 'MAR' AND
            $temp3 != 'APR' AND
            $temp3 != 'MAY' AND
            $temp3 != 'JUN' AND
            $temp3 != 'AUG' AND
            $temp3 != 'SEP' AND
            $temp3 != 'OCT' AND
            $temp3 != 'NOV' AND
            $temp3 != 'DEC' 
            ) OR $this->validateCharacters($temp4) OR
            ($temp5 != 'E')
        
        
        
        ){
            return false;
        }
        return true;
    }
}
