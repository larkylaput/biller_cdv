<?php

namespace App\Biller\Cdv\Validators;

use Throwable;
use Carbon\Carbon;
use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode3 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // PNB Credit Cards
            // $mainField = preg_replace('/\D/', '', $mainField); // remove all the non numberic characters
            if(
                $this->validateLength($mainField) and 
                $this->checkString($mainField) and
                $this->validateCharacters($mainField)
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

    private function validateCharacters($mainField) {
        
        if(!is_numeric($mainField) OR 
            strpos($mainField,'d') OR 
            strpos($mainField,'e') OR 
            strpos($mainField,'.') OR 
            strpos($mainField,' ') ){
            return false;

        }
        return true;
    }


    private function checkString($mainField){
        $i = 0;
        $product = 0;
        $sum = 0;
        $remainder = 0;
        $comp = 0;
        $new_mainField   = substr($mainField,0,9);
        $checkdigit   = substr($mainField,9,1);
        $weightdigits = str_split('298765432');
        $split_mainField = str_split($new_mainField);
        
        foreach($weightdigits as $key => $data){
            $product = $split_mainField[$key] * $data;
            $sum += $product;
        }
        
        $remainder = fmod($sum,11);
        if($remainder == 0 OR $remainder == 1){
            $comp = 0;
        }else{
            $comp = 11 - $remainder;
        }
        
        if($comp == $checkdigit){
            return true;
        }else{
            return false;
        }

    }


}
