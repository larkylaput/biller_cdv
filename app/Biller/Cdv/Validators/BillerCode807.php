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
            return $this->step1($mainField);

        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    private function step1($mainField)
    {
        $accountNumber = str_split(substr($mainField, 0, 9));
        $controlDigit = substr($mainField, 9, 1);

        $formula['Account Number'] = $mainField;
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
