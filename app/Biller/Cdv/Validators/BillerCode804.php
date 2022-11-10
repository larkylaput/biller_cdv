<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode804 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // PNB Credit Cards
            // $mainField = preg_replace('/\D/', '', $mainField); // remove all the non numberic characters
            if(
                $this->validateChars($mainField)  and 
                $this->validateCharacters($mainField) 
            ) {
                return true;
            }

        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }
    
    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateChars($mainField){
        
        if(strlen($mainField) == 7 AND (substr($mainField,0,1) == '2' or substr($mainField,0,1) == '9')){
            return true;
        }
        if(strlen($mainField) == 8 AND (substr($mainField,0,1) == '2' or substr($mainField,0,1) == '9')){
            return true;
        }


        if(strlen($mainField) == 14){
            $PD1 = substr($mainField,0,1) * 1;
            $PD2 = substr($mainField,1,1) * 2;
            $PD3 = substr($mainField,2,1) * 1;
            $PD4 = substr($mainField,3,1) *2;
            $PD5 = substr($mainField,4,1) * 1;
            $PD6 = substr($mainField,5,1) * 2;
            $PD7 = substr($mainField,6,1) * 1;
            $PD8 = substr($mainField,7,1) * 2;
            $PD9 = substr($mainField,8,1) * 1;
            $PD10 = substr($mainField,9,1) * 2;
            $PD11 = substr($mainField,10,1) * 1;
            $PD12 = substr($mainField,11,1) * 2;
            $PD13 = substr($mainField,12,1) * 1;
            $CHKDGT = substr($mainField,13,1) * 1;
       
            $PD13M1 = $PD1 % 10;
			$PD13M2 = $PD2 % 10;
			$PD13M3 = $PD3 % 10;
			$PD13M4 = $PD4 % 10;
			$PD13M5 = $PD5 % 10;
			$PD13M6 = $PD6 % 10;
			$PD13M7 = $PD7 % 10;
			$PD13M8 = $PD8 % 10;
			$PD13M9 = $PD9 % 10;
			$PD13M10 = $PD10 % 10;
			$PD13M11 = $PD11 % 10;
			$PD13M12 = $PD12 % 10;
			$PD13M13 = $PD13 % 10;

            if($PD1 < 10){
                $PD1 = $PD1;
            }else{
                $PD1 = $PD1 + 1;
            }

            if($PD2 < 10){
                $PD2 = $PD2;
            }else{
                $PD2 = $PD2 + 1;
            }

            if($PD3 < 10){
                $PD3 = $PD3;
            }else{
                $PD3 = $PD3 + 1;
            }

            if($PD4 < 10){
                $PD4 = $PD4;
            }else{
                $PD4 = $PD4 + 1;
            }

            if($PD5 < 10){
                $PD5 = $PD5;
            }else{
                $PD5 = $PD5 + 1;
            }

            if($PD5 < 10){
                $PD5 = $PD5;
            }else{
                $PD5 = $PD5 + 1;
            }

            if($PD6 < 10){
                $PD6 = $PD6;
            }else{
                $PD6 = $PD6 + 1;
            }

            if($PD7 < 10){
                $PD7 = $PD7;
            }else{
                $PD7 = $PD7 + 1;
            }

            if($PD8 < 10){
                $PD8 = $PD8;
            }else{
                $PD8 = $PD8 + 1;
            }

            if($PD9 < 10){
                $PD9 = $PD9;
            }else{
                $PD9 = $PD9 + 1;
            }

            if($PD10 < 10){
                $PD10 = $PD10;
            }else{
                $PD10 = $PD10 + 1;
            }

            if($PD11 < 10){
                $PD11 = $PD11;
            }else{
                $PD11 = $PD11 + 1;
            }

            if($PD12 < 10){
                $PD12 = $PD12;
            }else{
                $PD12 = $PD12 + 1;
            }

            if($PD13 < 10){
                $PD13 = $PD13;
            }else{
                $PD13 = $PD13 + 1;
            }

            $VAL_2 = $PD1 + $PD2 + $PD3 + $PD4 + $PD5 + $PD6 + $PD7 + $PD8 + $PD9 + $PD10 + $PD11 + $PD12 + $PD13;

            if(fmod($VAL_2,10) == 0){
                $VAL_3 = 10;
            }else{
                $VAL_3 = fmod($VAL_2,10);
            }

            $RES = 10 - $VAL_3;

            if($RES = $CHKDGT){
                return true;
            }else{  
                return false;
            }

        }
        
        else if(strlen($mainField) == 9){
            $WA = substr($mainField,0,2);
            if($WA == '65'){
                return false;
            }else{
                $Weights = '12121212';
                $WA = substr($mainField,8,1);
                $WD = 0;
                $I = 0;
                while($I < 8){
                    $WP = intval(substr($Weights,$I,1)) * intval(substr($mainField,$I,1));
                    if($WP > 9){
                        $A = substr($WP,0,1);
                        $B = substr($WP,1,1);
                        $WP = intval(intval($A) + intval($B));
                    }
                    $WD = $WP + $WD;
                    $I = $I + 1;
                }
                $R = fmod($WD,10);
                
                if($R <> 0){
                    $WR = 10 - $R;
                }else{
                    $WR = $R;
                }

                if($WR <> intval($WA)){
                    return false;
                }else{
                    return true;
                }

            }

        }else if(strlen($mainField) == 10){
            $I = 0;
            $WD = 0;
            $Weights = '121212121';
            $WA = substr($mainField,9,1);
            
            while($I < 9){

                $WP = intval(substr($Weights,$I,1)) * intval(substr($mainField,$I,1));

                if($WP > 9){
                    $A = substr($WP,0,1);
                    $B = substr($WP,1,1);
                    $WP = intval($A) + intval($B);
                }
                $WD = $WP + $WD;
                $I = $I + 1;
            }

            $R = fmod($WD,10);
            if($R <> 0){
                $WR = 10 - $R;
            }else{
                $WR = $R;
            }

            if($WR <> $WA){
                return false;
            }else{
                return true;
            }

        }else{
            return false;
        }
        
    }

}
