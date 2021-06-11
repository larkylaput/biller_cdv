<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1016 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // PNB Credit Cards
            $mainField = preg_replace('/\D/', '', $mainField);
            if (
                // $this->validateLength($mainField) and
                $this->validateNumbers($mainField) and
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
        if ($length <> 11) {
            return false;
        }
        return true;
    }

    private function validateSevenToTenCharacters($mainField)
    {
        $length = strlen($mainField);
        if ($length < 7 or $length > 10) {
            return false;
        }
        return true;
    }


    private function validateNumbers($mainField) {
        return is_numeric($mainField);
    }

    private function validateCharacters($mainField){

        $lastdgt = '';
        $stdntNum = 0;
        $ctr = 0;
        $i = 0;
        $validated = '';

        $lastdgt = substr($mainField,10,1); // it should be 11,1 php starts with 0
        $ctr = 10; 
        $i = 11;

        if($this->validateLength($mainField)){
            // mainField length == 11
            while($ctr > 0){
                $stdnt_substr = substr($mainField,$ctr,1);
                $stdntNum = $stdntNum + intval($stdnt_substr*$i);

                $ctr = $ctr - 1;
                $i++;

                $stdntNum = $stdntNum % 10;
                $validated = strval($stdntNum);

                if($validated == $lastdgt){

                    return true; // valid

                }else{
                    return false; // not valid

                }
            }

        }else{
            
            if($this->validateSevenToTenCharacters($mainField)){
                return true; //0 // valid
            }else{
                return false; //1 // not valid
            }

        }

    }

}
