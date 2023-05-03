<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode23 implements BillerCdvInterface
{   
    const WEIGHTS1 = [4,3,2,7,6,5,4,3,2];
    const WEIGHTS2 = [1, 2, 1, 2, 1, 2];
    const LENGTH = [10 , 7];
    public function validate($mainField, $amount): bool
    {
        try { //$mainField = preg_replace('/\D/', '', $mainField); 
            // PNB Credit Cards
            if (
                $this->validateLength($mainField)&&
                $this->validateCheckDigit($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }


    private function validateLength($mainField){
        $checkLength = strlen($mainField);
        return in_array($checkLength, self::LENGTH);
    }

    private function validateCheckDigit($mainField){
        $checkLength = strlen($mainField);
        if($checkLength === 10){
            $mainfield = substr($mainField,0,9);
            $lastDigit = substr($mainField,-1);

            return $this->validateDigitTen($mainfield, $lastDigit);
        }
        if($checkLength === 7){
            $mainfield = substr($mainField,0,6);
            $lastDigit = substr($mainField,-1);

            return $this->validateDigitSeven($mainfield, $lastDigit);
        }
    }

    private function validateDigitTen($mainfield, $lastDigit) {
        $sum = 0;
        $data;
        foreach (self::WEIGHTS1 as $key => $multiply) {
            $total = $multiply * $mainfield[$key];
            $formula['total'][] = "$multiply X ".$mainfield[$key]. " = $total";     
            $formula['sum'][] = "$total + $sum = " . ($sum + $total);
            $formula['data'][]=$key;
            $sum += $total;
        }
       dd($formula);
        $remainder = fmod($sum, 11);
        $checkDigit = 11 - $remainder;
        if ($remainder == 0) {
            $checkDigit = 0;
        } else if ($remainder == 1) {
            return false;
        }

        return $checkDigit == $lastDigit;
    }

    private function validateDigitSeven($mainfield, $lastDigit) {
        $sum = 0;
        foreach (self::WEIGHTS2 as $key => $multiply) {
            $total = $multiply *  $mainfield[$key];
            $formula['total'][] = "$multiply X ".$mainfield[$key]. " = $total";

            if($total > 9) {
                $splitTotal = str_split($total);
                foreach ($splitTotal as $value) {
                    $formula['sum'][] = "$sum + $value = " . ($sum + $value);
                    $sum += $value;
                }
            } else {
                $formula['sum'][] = "$total + $sum = " . ($sum + $total);
                $sum += $total;
            }
        }

        $remainder = fmod($sum, 10);
        $nextHighNum = ($sum - $remainder) + 10;
        $checkDigit = $nextHighNum - $sum;
        if($remainder == 0){
            $checkDigit = 0;
        }

        return $checkDigit == $lastDigit;
    }
}