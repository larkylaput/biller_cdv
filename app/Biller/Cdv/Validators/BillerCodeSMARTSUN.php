<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCodeSMARTSUN implements BillerCdvInterface
{
    CONST TEN=[4,3,2,7,6,5,4,3,2];
    CONST SEVEN=[1,2,1,2,1,2];
    public function validate($mainField, $amount): bool
    {
        try{
            $mainField = preg_replace('/\D/', '', $mainField);
            return $this->validateDigit($mainField);;
        }catch(\throwable $e){
            dd($e);
            throw new BillerValidatorException();
        }
        return false;

    }
    private function validateDigit($mainField){

        return $this->validateLength($mainField) == true?true:false;
    }
    private function validateLength($mainField){
        $lastDigit = substr($mainField,-1);
        $checkDigit = $mainField;
        if(strlen($mainField)==10){
           $checkDigit.substr(0,9);
           
           return $this->validateLengthTen($checkDigit,$lastDigit);
           
        }elseif(strlen($mainField)==7){
            $checkDigit.substr(0,6);
            return $this->validateLengthSeven($checkDigit,$lastDigit);
        }else{
            return false;
        }
    }
    private function validateLengthTen($checkDigit,$lastDigit){
        $sum = 0;
        foreach(self::TEN as $key => $multiply){
            $total = $multiply * $checkDigit[$key];
            $formula['total'][]= "$multiply X".$checkDigit[$key]." = $total"; 
            $sum += $total;
          
        }
        
        $remainder=fmod($sum,11);
        if($remainder==0){
            return $remainder = $lastDigit ? true:false;
        }elseif($remainder==1){
            return false;
        }else{
            $return= 11 -$remainder ;
            return $return == $lastDigit?true:false;
        }

    }
    private function validateLengthSeven($checkDigit,$lastDigit){
        $sum = 0;
        foreach(self::SEVEN as $key => $multiply){
            $total = $multiply * $checkDigit[$key];
            $formula['Seven'][]="$multiply X ".$checkDigit[$key]." = $total";
            if($total > 9){
                $splitTotal = str_split($total);
                foreach($splitTotal as $value){
                    $sum+=$value;
                }
            }else{
                $sum += $total;
            }
            
            $formula['Sum'][]= "Sum is".$sum;
        }
        
        $remainder =fmod($sum,10);
        $highNumber = ($sum -$remainder)+10;
        if($remainder ==0){
            return $remainder == $lastDigit ? true:false;
        }else{
            
            return ($highNumber - $sum)==$lastDigit ? true:false; 
        }
    }
}
