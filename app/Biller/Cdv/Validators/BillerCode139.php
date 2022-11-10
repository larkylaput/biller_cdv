<?php

namespace App\Biller\Cdv\Validators;

use Throwable;
use Carbon\Carbon;
use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode139 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // PNB Credit Cards
            $mainField = preg_replace('/\D/', '', $mainField); // remove all the non numberic characters
            if(
                $this->validateLength($mainField)  and 
                $this->validateLastCharacter($mainField) 
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
        if ($length <> 10 and $length <> 13 ){
            return false;
        }
        return true;
    }

    private function validateFirstTwoDigits($mainField)
    {
        $first_two_digits = substr($mainField,0,2);
        
        $length = strlen($mainField);
        if ($first_two_digits == '02' and 
            $first_two_digits == '03' and
            $first_two_digits == '04' and
            $first_two_digits == '05' and
            $first_two_digits == '06' 
            ){
            return false;
        }
        return true;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateLastCharacter($mainField){

        if(strlen($mainField) == 10){
            if($this->validateFirstTwoDigits($mainField)){

                $weight_split = str_split('003973973');
                $mainField_split = str_split(substr($mainField,0,9)); //9
                $a_product = [];
                $product = 0;
                $sum = 0;
                $mod_sum = 0;
                foreach($mainField_split as $key => $data){
                    $product = intval($data) * intval($weight_split[$key]);
                    array_push($a_product,$product);
                }

                $sum = array_sum($a_product);
                
                $mod_sum = fmod($sum,10);


                if($mod_sum == 0 OR $mod_sum <> intval(substr($mainField,9,1))){
                    return false;
                }else{
                    return true;
                }

            }else{

                return false;

            }

        }else if(strlen($mainField) == 13){
            
            if(substr($mainField,0,2) <> 01){

                return false;

            }else{

                $weightsplit = '003973973973';
                $mainFieldsplit = str_split(substr($mainField,0,12));
                
                $a_product = [];
                $product = 0;
                $sum = 0;
                $mod_sum = 0;
                foreach($mainFieldsplit as $key => $data){
                    $product = intval($data) * intval($weightsplit[$key]);
                    array_push($a_product,$product);
                }

                $sum = array_sum($a_product);
                $mod_sum = fmod($sum,10);
                if($mod_sum <> intval(substr($mainField,12,1))){
                    return false;
                }else{
                    return true;
                }

            }

        }


    }
}
