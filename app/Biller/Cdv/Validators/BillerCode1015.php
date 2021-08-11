<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1015 implements BillerCdvInterface
{
    const given_string = ['CSH','CHK'];
    const array_1 = ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30'];
    const array_2 = ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'];
    const array_3 = ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29'];
    public function validate($mainField, $amount): bool
    {
        try {
            // $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) AND 
                $this->checkChars($mainField) AND
                $this->validateCharacters($mainField) AND
                $this->validateStrings($mainField) 
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
        
        return $length <> 17 ? false : true;
    }

    private function validateCharacters($mainField)
    {
 
        if (!preg_match('/%[0-9A-Z]%/',substr($mainField,0,3)))
        {
            return true;
        }
        return false;


    }

    private function checkChars($mainField){
     
        if(intval(substr($mainField,3,4))  >= 1999 AND intval(substr($mainField,3,4))  <= 4999 AND in_array(substr($mainField,7,3),self::given_string)){ 
            if(is_numeric(substr($mainField,14,3)) AND substr($mainField,14,3) != '000'){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    private function validateStrings($mainField){
        if(in_array(substr($mainField,10,2),['04','06','09','11']) AND in_array(substr($mainField,10,2),self::array_1) ){
            return true;
        }else if(in_array(substr($mainField,10,2),['01','03','05','07','08','10','12']) AND in_array(substr($mainField,10,2),self::array_2) ){
            return true;
        }else if(substr($mainField,10,2) == '02' AND fmod(substr($mainField,3,4),4) == 0 AND in_array(substr($mainField,12,2),self::array_3) ){

            return true;
        }else{
            return false;
        }

    }
}
