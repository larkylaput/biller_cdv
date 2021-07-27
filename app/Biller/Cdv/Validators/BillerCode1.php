<?php

namespace App\Biller\Cdv\Validators;

use Throwable;
use Carbon\Carbon;
use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        
        try {
            // PNB Credit Cards
            // $mainField = preg_replace('/\D/', '', $mainField); // remove all the non numberic characters
            if(
                $this->validateLength($mainField) and 
                $this->validateFirstTwoDigits($mainField) and
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
        if ($length <> 10) {
            return false;
        }
        return true;
    }

    private function validateFirstTwoDigits($mainField) {
        
        if(substr($mainField,0,2) <> 07){
            return false;
        }
        return true;
    }

    private function validateChars($mainField){

        $new_mainField = substr($mainField,0,9);
        $checkdigit = substr($mainField,9,1);
        $i = 0;
        $sum = 0;
        $comp = 0;
        $product = 0;
        $split_mainField = str_split(substr($mainField,2,7));
        
        $split_weight = str_split('8765432');
        
        foreach($split_weight as $key => $data){
            $product = $split_mainField[$key] * $data;
            $sum = $sum + $product;
        }
        
        $rem = fmod($sum,11);

 
        if($rem == 0){
            $comp = 0;
        }else if($rem == 10){
            $comp = 1;
        }else{
            $comp = 11 - $rem;
        }
        
        if($comp == $checkdigit){
         
            return true;
        }else{
         
            return false;
        }

    }


}
