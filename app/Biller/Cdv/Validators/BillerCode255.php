<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode255 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        
        try {
            // $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) AND 
                $this->validateChars($mainField)
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

        // dd($this->validateCharacters($temp4));
        // 
        if(($temp3 == 'JAN' OR
        $temp3 == 'FEB' OR
        $temp3 == 'MAR' OR
        $temp3 == 'APR' OR
        $temp3 == 'MAY' OR
        $temp3 == 'JUN' OR
        $temp3 == 'AUG' OR
        $temp3 == 'SEP' OR
        $temp3 == 'OCT' OR
        $temp3 == 'NOV' OR
        $temp3 == 'DEC' ) AND $temp1 != '0' AND $temp5 == 'P' AND $temp2 != '0' AND $temp4 != '0'
        ){
            return true;

        }else{
            return false;
        }
        
    }
}
