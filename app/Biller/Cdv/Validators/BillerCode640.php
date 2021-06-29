<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode640 implements BillerCdvInterface
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
        if ($length <> 10) {
            return false;
        }
        return true;
    }

    private function validateNumberic($mainField) {
        return is_numeric($mainField);
    }

    private function validateCharacters($mainField){

        $Weights = '987654321';
        $Weights_split = str_split($Weights);
        $mainField_split = str_split(substr($mainField,0,9));
        $mainField_check_digit = substr($mainField,9,1);
        $result = [];
        foreach($mainField_split as $key => $data){

            $t = 0;
            $a_val = [];
    
            // multiply the digit based on corresponding index.
            $t = intval($data) * intval($Weights_split[$key]);
            
            if(strlen($t) > 1){ 
                // if the digit in index is greater than 1, split the digits and get the sum.
                $a_val = str_split($t);
                array_push($result,intval(array_sum($a_val)));
            }else{
                array_push($result,intval($t));
            }
          
        }

        $final_result = [];
        foreach($result as $key => $data){

            $value = [];

            if(strlen($data) > 1){ 
                $value = str_split($data);
                array_push($final_result,intval(array_sum($value)));
            }else{
                array_push($final_result,intval($data));
            }

        }

        $higher_number = array_sum($final_result);
        $remainder = fmod($higher_number,10);
        $check_digit = 10 - $remainder;

        if($check_digit == $mainField_check_digit){
            return true;
        }
        return false;

    }
}
