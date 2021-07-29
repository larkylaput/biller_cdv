<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1015 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) AND 
                $this->validateCharacters($mainField) AND
                $this->checkChars($mainField) 
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
        
        return $length <> 17 ? false : true;
    }

    private function validateCharacters($mainField)
    {
        dd(is_numeric(substr($mainField,10,7)) );
        if(
            ctype_alpha(substr($mainField,0,1)) OR
            is_numeric(substr($mainField,1,6)) OR
            ctype_upper(substr($mainField,7,3)) OR
            is_numeric(substr($mainField,10,7)) 
        ){
            return true;

        }

        return false;
        
    }

    private function checkChars($mainField){
     

        if(intval(substr($mainField,3,4))  > 2000 AND intval(substr($mainField,3,4))  < 2050 ){ // year
            if(intval(substr($mainField,10,2)) >= 1 AND intval(substr($mainField,10,2)) <= 12){ // month
                if(intval(substr($mainField,12,2)) >= 1 AND intval(substr($mainField,12,2)) <= 31){ // day
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
