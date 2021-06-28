<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode692 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // PNB Credit Cards
            // $mainField = preg_replace('/\D/', '', $mainField); // remove all the non numberic characters
            if(
                $this->validateLength($mainField) and
                $this->validateCharacters($mainField) and
                $this->validateNumberic($mainField)
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

    private function validateNumberic($mainField) {
        return is_numeric($mainField);
    }

    private function validateCharacters($mainField){
        
        $SubGetDigit1 = 0;
        $TalGetDigit1 = 0;
        $SubGetDigit2 = 0;   
        $TalGetDigit2 = 0;
        $SubGetDigit3 = 0; 
        $TalGetDigit3 = 0;
        $SubGetDigit4 = 0;
        $TalGetDigit4 = 0;
        $SubGetDigit5 = 0;
        $TalGetDigit5 = 0;
        $SubGetDigit6 = 0;
        $TalGetDigit6 = 0;
        $SubGetDigit7 = 0; 
        $TalGetDigit7 = 0;
        $SubGetDigit8 = 0;
        $TalGetDigit8 = 0;
        $SubGetDigit9 = 0;
        $TalGetDigit9 = 0;
        $SubGetDigit = 0;
        $TalGetDigit = 0;
        $TotalDigit = 0;

        //1
        $subgetDigit1 = fmod(intval(substr($mainField,0,1) + 10),10);
        if(intval($subgetDigit1) == 0){
            $TalGetDigit1 = fmod((int)10 * (int)3,11);
        }else{
            $TalGetDigit1 = fmod(((int)$SubGetDigit1 * 3),11);
        }

        //1
        $SubGetDigit2 = fmod(intval(substr($mainField,0,1) + $TalGetDigit1),10);
        if(intval($SubGetDigit2) == 0){
            $TalGetDigit2 = fmod((int)10 * (int)3,11);
        }else{
            $TalGetDigit2 = fmod(((int)$SubGetDigit2 * 3),11);
        }
        
        //3
        $SubGetDigit3 = fmod(intval(substr($mainField,0,1) + $TalGetDigit2),10);
        if(intval($SubGetDigit3) == 0){
            $TalGetDigit3 = fmod((int)10 * (int)3,11);
        }else{
            $TalGetDigit3 = fmod(((int)$SubGetDigit3 * 3),11);
        }

        //4
        $SubGetDigit4 = fmod(intval(substr($mainField,0,1) + $TalGetDigit3),10);
        if(intval($SubGetDigit4) == 0){
            $TalGetDigit4 = fmod((int)10 * (int)3,11);
        }else{
            $TalGetDigit4 = fmod(((int)$SubGetDigit4 * 3),11);
        }

        //5
        $SubGetDigit5 = fmod(intval(substr($mainField,0,1) + $TalGetDigit4),10);
        if(intval($SubGetDigit5) == 0){
            $TalGetDigit5 = fmod((int)10 * (int)3,11);
        }else{
            $TalGetDigit5 = fmod(((int)$SubGetDigit5 * 3),11);
        }

        //6
        $SubGetDigit6 = fmod(intval(substr($mainField,0,1) + $TalGetDigit5),10);
        if(intval($SubGetDigit6) == 0){
            $TalGetDigit6 = fmod((int)10 * (int)3,11);
        }else{
            $TalGetDigit6 = fmod(((int)$SubGetDigit6 * 3),11);
        }

        //7
        $SubGetDigit7 = fmod(intval(substr($mainField,0,1) + $TalGetDigit6),10);
        if(intval($SubGetDigit7) == 0){
            $TalGetDigit7 = fmod((int)10 * (int)3,11);
        }else{
            $TalGetDigit7 = fmod(((int)$SubGetDigit7 * 3),11);
        }

        //8
        $SubGetDigit8 = fmod(intval(substr($mainField,0,1) + $TalGetDigit7),10);
        if(intval($SubGetDigit8) == 0){
            $TalGetDigit8 = fmod((int)10 * (int)3,11);
        }else{
            $TalGetDigit8 = fmod(((int)$SubGetDigit8 * 3),11);
        }

        //9
        $SubGetDigit9 = fmod(intval(substr($mainField,0,1) + $TalGetDigit8),10);
        if(intval($SubGetDigit9) == 0){
            $TalGetDigit9 = fmod((int)10 * (int)3,11);
        }else{
            $TalGetDigit9 = fmod(((int)$SubGetDigit9 * 3),11);
        }

        $SubGetDigit = fmod(intval(substr($mainField,0,1) + $TalGetDigit9),10);
        if(intval($SubGetDigit) == 0){
            $TalGetDigit = fmod((int)10 * (int)3,11);
        }else{
            $TalGetDigit = fmod(((int)$SubGetDigit * 3),11);
        }         


        $TotalDigit = fmod(11 - intval($TalGetDigit),10);
        if($TotalDigit = intval(substr($mainField,11,1))){
            return true;
        }else{
            return false;
        }

    }



}