<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode909 implements BillerCdvInterface
{
    const key_char = ['FL','LB','AL','RN','OT'];
    const key_char1 = ['OP','CB','FF'];
    public function validate($mainField, $amount): bool
    {

        try {
            // $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) AND 
                $this->validateCharacters($mainField) AND 
                $this->checkDigits($mainField)
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
        
        return $length <> 14 ? false : true;
    }

    private function validateCharacters($mainField)
    {   
        
        // dd(substr($mainField,8,6));
        $b_failed = false;

        // validate if 1st and 6th character is numeric
        if(ctype_upper(substr($mainField,6,2)) AND is_numeric(substr($mainField,0,6)) AND is_numeric(substr($mainField,8,6))){
            $b_failed = true;
        }else{
            return $b_failed = false;
        }
        return $b_failed;
    }

    function checkDigits($mainField){
        
        if(in_array(substr($mainField,6,2),self::key_char)){
            if(intval(substr($mainField,8,6)) < 000000 OR intval(substr($mainField,8,6)) > 999999){
                return false;
            }else{
                return true;
            }
            return true;

        }else if(in_array(substr($mainField,6,2),self::key_char1)){

            if(substr($mainField,8,6) == '000000'){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

}
