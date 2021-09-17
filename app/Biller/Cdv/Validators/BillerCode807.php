<?php

namespace App\Biller\Cdv\Validators;

use DateTime;
use Carbon\Carbon;
use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode807 implements BillerCdvInterface
{
    CONST WEIGHT = [10,8,7,6,5,4,3,2,1];

     public function validate($mainField, $amount) : bool
    {
        $mainField = str_replace(' ', '', $mainField);
        try {
            if (
                $this->step1($mainField) &&
                $this->validateLength($mainField)
            ) {
                return true;
            }
            // return $this->step1($mainField);

        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateLength($mainField) {
        $length = strlen($mainField);
        return ($length === 10 || $length === 16) ? true : false;
    }

    private function step1($mainField)
    {
        $length = strlen($mainField);

        $accountNumber = str_split(substr($mainField, 0, 9));

        if ($length === 16) {
            $mainField = substr($mainField, 1, 10);
            $accountNumber = str_split(substr($mainField, 0, 9));
        }
        
        $controlDigit = substr($mainField, -1);

        $formula['Account Number'] = $mainField;
        $formula['Split Number'] = $accountNumber;
        $formula['Control Digit'] = $controlDigit;
        $product = 0;
        $sum = 0;
        foreach ($accountNumber as $key => $value) {
            $product = $value * Self::WEIGHT[$key];
            $formula['formula'][] = "$value X ".Self::WEIGHT[$key]. " = $product";
            $sum += $product;
        }

        $remainder = fmod($sum, 9) ?: 9;
        $formula['formula'][] = "$sum / 9 = ". intval($sum/9).".$remainder";
        $formula['formula'][] = "Remainder: $remainder";
        $formula['formula'][] = "Control Number: $controlDigit";
        $formula['formula'][] = $controlDigit==$remainder;
        // dd($controlDigit, $remainder, $controlDigit == $remainder, $formula);
        return $controlDigit == $remainder;
    }

    
}
