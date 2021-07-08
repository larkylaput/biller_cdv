<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Carbon\Carbon;

class BillerCode2 implements BillerCdvInterface
{
    CONST WEIGHT = [8,7,6,5,4,3,2,1];

    public function validate($mainField, $amount): bool
    {
        try {
            $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) AND 
                $this->validateCharacters($mainField) AND
                $this->validateFormat($mainField)
            ) {
                if($this->validateCheckDigit($mainField)){
                    return true;
                }
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField)
    {
        $length = strlen($mainField);
        
        return $length === 14 ? true : false;
    }

    private function validateCharacters($mainField)
    {
        return is_numeric($mainField);
    }

    private function validateFormat($mainField)
    {
        $first2digit = substr($mainField, 0, 2);
        $expiryDate = substr($mainField, -4, 4);

        $first2Exp = substr($expiryDate, 0, 2);
        $last2Exp = substr($expiryDate, 2, 2);

        if(!($first2digit == '02' || $first2digit == '03' || $first2digit == '04')){
            return false;
        }
        $yearNow = Carbon::now()->format('Y');
        $monthNow = Carbon::now()->format('m');

        $dateNow = Carbon::now();

        if($first2Exp == '01' AND $monthNow == '12'){
            $yearNow += 1;
        }
        if($first2Exp == '12' AND $monthNow == '01'){
            $yearNow -= 1;
        }
        $date = Carbon::parse($first2Exp . "/" . $last2Exp . "/" . $yearNow);
        return $date->gte($dateNow);
    }

    private function validateCheckDigit($mainField)
    {
        $accountNumber = str_split(substr($mainField, 2, 7));
        $checkDigit = substr($mainField, 9, 1);

        $formula['Account Number'] = $mainField;
        $formula['Check Digit'] = $checkDigit;
        
        $product = 0;
        $sum = 0;

        foreach ($accountNumber AS $key => $value) {
            $product = $value * Self::WEIGHT[$key];

            $formula['Product'][] = "$value X ".Self::WEIGHT[$key]. " = $product";

            $formula['Summation'][] = "($product) $sum + $product = " . ($sum + $product);

            $sum += $product;
        }
        $remainder = fmod($sum, 11);
        $computed = 11 - $remainder;

        if ($remainder == 0) {
            $computed = 0;
        }
        if ($remainder == 1) {
            return false;
        }
        if ($remainder == 10) {
            $computed = 1;
        }
        
        $formula['Check'][] = "Modulo: $sum % 11 = $remainder";
        $formula['Check'][] = "Checker: 11 - $remainder = $computed";
        $formula['Check'][] = $checkDigit==$computed;

        // dd($formula);
        
        return $checkDigit == $computed;
    }
}
