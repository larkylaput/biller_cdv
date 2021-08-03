<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode82 implements BillerCdvInterface
{
    const weightVal = '1212';
    const Divisor = 10;
    public function validate($mainField, $amount): bool
    {
        dd($this->validateChars($mainField));
        try {
            // $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) AND 
                $this->validateChars($mainField) AND 
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
        
        return $length === 5 ? true : false;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }

    private function validateChars($mainField){
        
        $split_weight = str_split(self::weightVal);
        $split_mainField = str_split(substr($mainField,0,4));
        $LastDigit = substr($mainField,4,1);
        $product = 0;
        $total = 0;
        $reimainder = 0;
        $a = [];
        foreach($split_mainField as $key => $data){

            $product = $split_weight[$key] * $data;
           
            array_push($a,$product);
            $total = $total + $product;

        }
        

        $reimainder = fmod($total,self::Divisor);
 
        if($reimainder <> 0){
            $total = self::Divisor - $reimainder;
        }else{
            $total = 0;
        }

        if($total == $LastDigit){
            return true;
        }else{
            return false;
        }


    }       
}
