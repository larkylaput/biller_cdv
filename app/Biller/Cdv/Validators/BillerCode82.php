<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode82 implements BillerCdvInterface
{   
    const WEIGHTS = [1,2,1,2];

    public function validate($mainField, $amount): bool
    {
        try {
            // PNB Credit Cards
            if (
                $this->validateLength($mainField)&&
                $this->checkDigitValidation($mainField)&&
                $this->validateCharacters($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateCharacters($mainField){
        return is_numeric($mainField);
    }

    private function validateLength($mainField)
    {
        $check_length = strlen($mainField);
        if($check_length === 5){
            return true;
        }
        return false;
    }
    
    private function checkDigitValidation($mainField)
    {
        $mainfield = substr($mainField,0,4);
        $lastDigit = substr($mainField,-1);
        
        $sum = 0;
        //multiply to weight
        foreach (self::WEIGHTS as $key => $multiply) {
            $total = $multiply *  $mainfield[$key];
            $formula['total'][] = "$multiply X ".$mainfield[$key]. " = $total";

            if($total > 9) {
                $splitTotal = str_split($total);
                foreach ($splitTotal as $value) {
                    $formula['sum'][] = "$sum + $value = " . ($sum + $value);
                    $sum += $value;
                }
            } else {
                $formula['sum'][] = "$total + $sum = " . ($sum + $total);
                $sum += $total;
            }
        }
        
        $remainder = fmod($sum, 10); //get remainder
        $checkDigit =  10 - $remainder;
        
        if($checkDigit == 10){
            $checkDigit = 0; // if remainder is zero
        }
        
        return $checkDigit == $lastDigit;   
    }
}
