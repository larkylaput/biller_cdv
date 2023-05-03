<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCodeMBTC implements BillerCdvInterface
{    
    CONST WEIGHT = [1,1,1,1,1,1,1,1];
    public function validate($mainField, $amount): bool
    {
        try{
            $mainField = preg_replace('/\D/', '', $mainField);
            return $this->CheckLength($mainField);
        }catch(\throwable $e){
            throw new BillerValidatorException();
        }
        return false;
    }    
    public function CheckLength($mainField){
        return (strlen($mainField)==16)?true:false;
    }
    public function ComputeDigit($mainField){
        
        foreach(self::WEIGHT as $key => $add){

            $incrementKeys = $key +1;
            $sum = $add + $mainField;


        }
        return false;
    }
}
