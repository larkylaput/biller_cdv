<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1065 implements BillerCdvInterface
{
    const weight = '1212121212';
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
        if ($length <> 12) {
            return false;
        }
        return true;
    }

    private function validateCharacters($mainField) {
 
        return (substr($mainField,0,3) == '268'? true : false);
    }

    private function validateChars($mainField){
        // dd(substr($mainField,1,1));
        $split_weight = str_split(self::weight);
        $split_mainField = str_split($mainField);
        $product = '';
        $a_product = [];
        $total = '';
        $pos1 = '';
        $mod = '';
        $lastDigit = '';
        foreach($split_weight as $key => $data){
            $product = $split_mainField[$key] * $data;
           
            if(strlen($product) == 2){

                $pos1 = substr($product,0,1) + substr($product,1,1);

            }else if(strlen($product) == 1){

                $pos1 = $product;
            }


            array_push($a_product,$pos1);


        }
        
        $a_product_sum = array_sum($a_product);
        $mod = fmod($a_product_sum,10);

        $lastDigit = 10 - $mod;

        $lastDigit = (strlen($lastDigit) == 2 ? substr($lastDigit,1,1): $lastDigit);

        if($lastDigit == substr($mainField,11,1)){
            return true;
        }else{  
            return false;
        }

    }
}
