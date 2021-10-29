<?php

namespace App\Biller\Cdv\Validators;

use Throwable;
use Carbon\Carbon;
use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode714 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) && 
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
        return $length >= 1 && $length <= 30 ? true : false;
    }

    private function validateCharacters($mainField)
    {
        // alphanumeric with allow spaces
        return preg_match('/^[a-zA-Z0-9\s]+$/', $mainField) ? true : false;
    }
    // public function validate($mainField, $amount): bool
    // {   
    //     try {
    //         // $mainField = preg_replace('/\D/', '', $mainField);
    //         if (
    //             $this->validateLength($mainField) AND 
    //             $this->checkChracters($mainField)
    //         ) {
    //             return true;
    //         }
    //     } catch (\Throwable $e) {
    //         throw new BillerValidatorException();
    //     }
        
    //     return false;
    // }

    // private function validateLength($mainField)
    // {
    //     $length = strlen($mainField);
    //     if ($length < 2 or $length > 16 or empty($mainField) ) {
    //         return false;
    //     }
    //     return true;
    // }

    // private function checkChracters($mainField){

    //     $validChars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    //     $result = true;
    //     if(substr($mainField,0,3) == 'RVB' OR substr($mainField,0,3) == 'SLV'){
    //         $ctr = 3;
    //         while($ctr < strlen($mainField)){
    //             if(strpos($validChars,substr($mainField,$ctr,1)) === false){
    //                 return false;
    //             }
    //             $ctr++;
    //         }
    //         return true;
    //     }else if(substr($mainField,0,2) == 'VW' OR substr($mainField,0,2) == 'TB'){
    //         $ctr = 2;
    //         while($ctr < strlen($mainField)){
    //             if(strpos($validChars,substr($mainField,$ctr,1)) === false){
    //                 return false;
    //             }
    //             $ctr++;
    //         }
    //         return true;
    //     }else{
    //         return false;
    //     }
    // }
}