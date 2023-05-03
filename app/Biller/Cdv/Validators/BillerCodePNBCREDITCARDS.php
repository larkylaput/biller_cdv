<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCodePNBCREDITCARDS implements BillerCdvInterface
{
    CONST WEIGHT = [2,1,2,1,2,1,2,1,2,1,2,1,2,1,2];
    CONST BIN = [512634,513208,513208,625013,521387,545143,552280,533639,
                553294,543761,622145,533653,524046,548509,488907,488908,492161];

    public function validate($mainField, $amount): bool
    {
        try {
            
            $mainField = preg_replace('/\D/', '', $mainField);
            return $this->validateLength($mainField,16)&& $this->validateDigit($mainField);

        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField,$length){
        return (strlen($mainField)===$length)?true:false;
    }

    private function validateDigit($mainField){
        if($this->validateBin($mainField)==true){
            $lastDigit = substr($mainField, -1);
            $mainField = substr($mainField,0,15);

            return $this->validateCheckDigit($mainField,$lastDigit) ?true:false;
        }else{
            return false;
        }
    }

    private function validateBin($mainField){
        return in_array(substr($mainField,0,6),self::BIN);
    }

    private function validateCheckDigit($mainField,$lastDigit){
        $sum=0;
    
        foreach(self::WEIGHT as $key => $multiply){
            $total = $multiply * $mainField[$key];
            $totalComputation;
            $formula['total'][]="$multiply X".$mainField[$key]." = $total";
            if($total > 9){
                $splitTotal = str_split($total);
                foreach($splitTotal as $value){
                    $formula['sum'][]="$sum + $value =".($sum + $value);
                    $sum += $value;
                }
            }else{
                $formula['sum'][] = "$total + $sum = " . ($sum + $total);
                $sum += $total;

            }
        }
        $fmod =fmod($sum,10);
        $highNumber=($sum -$fmod)+10;
        if($fmod == 0){
            return $fmod == $lastDigit ? true :false;
        }else{
            $return = $highNumber - $sum;
            return $return == $lastDigit ? true:false;
        }
        
       
    }

 
}
