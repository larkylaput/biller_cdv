<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode975 implements BillerCdvInterface
{
    const valid_chars = ['AB','AL','BB','BT','BO','BL','CL','CR','FL','LA','MF','MP','OF','PL','QL','SL','SM'
    ,'RS','KS','ST','ES','HS','HO','SS'
    ,'MT','HG','FA','VS','FM','PG','SD','MF','AR','CA','MS','NZ','SM'];
    public function validate($mainField, $amount): bool
    {

        // dd($this->validateCharacters($mainField));
        try {
            // $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) AND 
                $this->validateCharacters($mainField)
                // $this->validateChars($mainField) AND 
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

    // private function validateLength($mainField)
    // {
    //     $length = strlen($mainField);
        
    //     return $length === 8 ? true : false;
    // }

    // private function validateCharacters($mainField)
    // {
    //     if(in_array(substr($mainField,0,2),self::valid_chars)){
    //         return true;
    //     }
    //     return false;
    // }

    // private function validateChars($mainField){
    //     if(is_numeric(substr($mainField,2,6))){
    //         return true;
    //     }
    //     return false;
    // }
}
