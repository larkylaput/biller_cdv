<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode24 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        
        try {
            // PNB Credit Cards
            $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) and
                $this->validatePrefixNumber($mainField) and
                $this->validateStudentNumber($mainField)
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
        if ($length <> 16) {
            return false;
        }
        return true;
    }

    private function validatePrefixNumber($mainField){

        
        $prefixNumber = '87574621';

        $studentNumber = substr($mainField,0,8);

        if($prefixNumber == $studentNumber){
            return true;
        }

        return false;

    }

    private function validateStudentNumber($mainField){
        // return 'fas';
        $numberWeights = '212121212121212'; // number weights
        $substr_chars15 = substr($mainField,0,15); // get all the digits before dash (-) character
        $a_splitStudentNumber1 = str_split(intval($substr_chars15)); // split all first 15 digits
        $a_splitNumberWeights = str_split(intval($numberWeights)); // split number weights
        $mainField_checkDigit = substr($mainField,15,1); // get the check digit of given number
        $final_value = [];
        // return $a_splitStudentNumber1;
        foreach($a_splitStudentNumber1 as $key => $data){

            $t = 0;
            $a_val = [];
        
            // multiply the digit based on corresponding index.
            $t = intval($data) * intval($a_splitNumberWeights[$key]);

            if(strlen($t) > 1){ 
                // if the digit in index is greater than 1, split the digits and get the sum.
                $a_val = str_split($t);
                array_push($final_value,intval(array_sum($a_val)));
            }else{
                array_push($final_value,intval($t));
            }
        }
        // to get the nearest higher number ending with digit 0 
        $higher_number = ceil(array_sum($final_value) / 10) * 10;

        $checkDigit = (int)$higher_number - array_sum($final_value);

        if($checkDigit == $mainField_checkDigit){
            return true;
        }

        return false;;
    }

}
