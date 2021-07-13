<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode25 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        
        try {
            // PNB Credit Cards
            $mainField = preg_replace('/\D/', '', $mainField); // remove all the non numberic characters
            if(
                $this->validateLength($mainField)  and 
                $this->validateCharacters($mainField) and
                $this->validateChars($mainField) 
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
        $length = strlen($mainField);
        if ($length > 9 or $length == '000000000') {
            return false;
        }
        return true;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateChars($mainField){

        $split_weight = str_split('32765432');
        $split_mainField = str_split(substr($mainField,0,8));
        $checkdigit = substr($mainField,8,1);
        $product = 0;
        $sum = 0;
        $reminder = 0;
        foreach($split_weight as $key => $data){
            $product = $split_mainField[$key] * $data;
            $sum = $sum + $product;

        }

        $reminder = fmod($sum,11);

        if($reminder == 0){
            $comp = 0;
        }else if($reminder == 1){
            return false;
        }else{
            $comp = 11 - $reminder;
        }
        
        if($comp == $checkdigit){
            return true;
        }else{
            return false;
        }

        
    }
}
