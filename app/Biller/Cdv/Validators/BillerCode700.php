<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode700 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        
        try {
            // PNB Credit Cards
            // $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) and
                $this->validateCharacter($mainField)
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

    private function validateCharacter($mainField){

        $m = 0;
    
        $d='0123456789123406789523401789563401289567401239567859876043216598710432765982104387659321049876543210';

        $p='01234567891576283094580379614289160435279453126870428657390127938064157046913258';
    
        $inv='0432156789';

        $c = 0;
        $reversed_string = strrev($mainField);
        $length_string = strlen($reversed_string);
        $split_string = str_split($reversed_string);
        $i = 0;
        $c = 0;
        // foreach($split_string as $data){
        while($i < $length_string){
            $m_substr2 = 0;
            $m_substr_param1 = (($i%8)*10);
            // $m_substr2 = substr($reversed_string,$i+1,1);
            $m_substr2 = substr($reversed_string,$i,1); // i removed +1 because in php first param in substr starts with 0
            // $m = substr($p,((int)$m_substr_param1+(int)$m_substr2)+1,1);
            $m = substr($p,((int)$m_substr_param1+(int)$m_substr2),1); // i removed +1 because in php first param in substr starts with 0
            
            $c_substr_param1 = ($c*10+$m+1);
            $c = (int)substr($d,$c_substr_param1,1);

            $i++;
            
        }
        if($c == 0){
            return true;
        }else{
            return false;
        }

    }

}
