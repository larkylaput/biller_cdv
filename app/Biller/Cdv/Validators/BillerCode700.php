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

            // SET @m = CAST(	substring(@p,((@i%8)*10) + CAST(substring(@n,@i+1,1) as tinyint)	+1,1) as tinyint)

            // SET @c = CAST(substring(@d,(@c*10+@m+1),1) as tinyint)


            // SET @i=@i+1

            $m = intval(    substr( $p, (($i%8)*10) + intval(substr($reversed_string,$i,1)) , 1)); // i removed +1 because in php first param in substr starts with 0
            
            $c = (int)substr($d,($c*10+$m),1);

            $i++;
            
           
           
        }

        if($c == 0){
            return true;
        }else{
            return false;
        }

    }

}
