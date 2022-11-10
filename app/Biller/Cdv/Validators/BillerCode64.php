<?php

namespace App\Biller\Cdv\Validators;

use Throwable;
use Carbon\Carbon;
use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode64 implements BillerCdvInterface
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
        if ($length <> 10) {
            return false;
        }
        return true;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateChars($mainField){

        $new_mainField = str_split(substr($mainField,0,9));
        $checkdigit = substr($mainField,9,1);
        $sum = 0;
        $product = 0;
        foreach($new_mainField as $key => $data){
            $sum += $data;
        }
        $product = $sum * 3;

        $split_product = str_split($product);
        $total = 0;
        $remainder = 0;
        $comp = 0;
        foreach($split_product as $key => $data){
            $total += $data;
        }
        $remainder = fmod($total,9);
        
        $comp = 7 - $remainder;
        
        if($comp == $checkdigit){
            return true;
        }else{
            return false;
        }


    }
}
