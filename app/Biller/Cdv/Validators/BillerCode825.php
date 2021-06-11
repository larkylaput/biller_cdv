<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode825 implements BillerCdvInterface
{
    CONST FORMAT = [2, 7];

    public function validate($mainField, $amount): bool
    {
        try {
            $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) and 
                $this->validateFirstFourDigits($mainField) and 
                $this->validateCheckDigit($mainField)
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

        return $length === 11 ? true : false;
    }

    private function validateFirstFourDigits($mainField) {
        $firstFour = substr($mainField, 0, 4);

        return $firstFour == 2002 ? true : false;
    } 

    private function validateCheckDigit($mainField) {
        $accountNumber = str_split(substr($mainField, 0, 10));
        $checkDigit = substr($mainField, 10, 1);
        $sum = 0;
        $index = 0;

        $accountNumberRev = array_reverse($accountNumber);

        for($i = 11; $i <= 20; $i++){
            $sum += (int) $accountNumberRev[$index] * $i;

            $index++;
        }

        // dd($accountNumber, $accountNumberRev, $checkDigit, $sum);

        if($checkDigit  == ($sum % 10)){
            return true;
        }
        else{
            return false;
        }

    } 

    private function validateHypen($digit)
    {
        return $digit === '-' ? true : false;
    }
}