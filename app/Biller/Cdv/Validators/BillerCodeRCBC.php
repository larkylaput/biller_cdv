<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCodeRCBC implements BillerCdvInterface
{
    CONST BIN =[544195,545369,547581,517968,517982,541509
    ,541512,524302,553283,552366,429382,431170,457358,436861, 427934,
    356286, 356288, 356275, 356276,625007,917010];
    CONST WEIGH = [2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2];
    
    public function validate($mainField, $amount): bool
    {
     try{
        $mainField = preg_replace('/\D/', '', $mainField);
        return $this->validateLength($mainField) && $this->validateBIN($mainField) 
        && $this->validateDigit($mainField);
     }catch(\throwable $e){
        
        throw new BillerValidatorException();
     }

    }
    private function validateLength($mainField){
        return strlen($mainField) == 16 ? true:false;
    }
    private function validateBIN($mainField){
        $Bin = substr($mainField,0,6);
        
        return in_array($Bin,self::BIN);
    }
    private function validateDigit($mainField){
        $lastDigit=substr($mainField,-1);
        $checkDigit = substr($mainField,0,15);
        $sum = 0;
        foreach(self::WEIGH as $key => $multiply){
            $total = $multiply * $checkDigit[$key];
            if($total > 9){
                $splitTotal = str_split($total);
                foreach($splitTotal as $value){
                    $formula['sum'][]="$sum + $value =".($sum + $value);
                    $sum += $value;
                }
            }else{
                $sum += $total;
            }

        }
        $remainder = fmod($sum,10);
        $highNumber= ($sum - $remainder) +10;
        $checkDigits = ($highNumber - $sum);
        return $checkDigits == $lastDigit ? true:false;
    }
    
}
