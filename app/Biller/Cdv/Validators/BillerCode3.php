<?php

namespace App\Biller\Cdv\Validators;

use Throwable;
use Carbon\Carbon;
use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode3 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if(
                $this->validateLength($mainField) && 
                $this->validateCharacters($mainField) &&
                $this->validateDigit($mainField) &&
                $this->validateFormat($mainField)
            ) {
                return true;
            }

        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateLength($mainField) {
        $length = strlen($mainField);
        return ($length >= 8 && $length <= 10) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateDigit($mainField) {
        $length = strlen($mainField);
        if ($length == 10) {
            return (in_array(substr($mainField, 0, 2), ['25', '28'])) ? true : false;
        } else if ($length == 9) {
            return (in_array(substr($mainField, 0, 3), ['000', '110', '220', '330'])) ? true : false;
        } else if ($length == 8) {
            return (in_array(substr($mainField, 0, 3), ['110', '200', '881']) || (substr($mainField, 0, 3) >= '001' && substr($mainField, 0, 3) <= '018') || (substr($mainField, 0, 3) >= '082' && substr($mainField, 0, 3) <= '099')) ? true : false;
        }
    }

    private function validateFormat($mainField){
        if (strlen($mainField) == 10) {
            $product = 0;
            $sum = 0;
            $remainder = 0;
            $comp = 0;
            $new_mainField = substr($mainField,0,9);
            $check_digit = substr($mainField, -1);

            $weight_digits = str_split('123456789');

            $split_mainField = str_split($new_mainField);
            
            foreach($weight_digits as $key => $data){
                $product = $split_mainField[$key] * $data;
                $sum += $product;
            }
            
            $remainder = fmod($sum,11);
            if($remainder == 10) {
                $comp = 0;
            } else {
                $comp = $remainder;
            }

            return ($comp == $check_digit) ? true : false;
        }

        return true;
    }
}
