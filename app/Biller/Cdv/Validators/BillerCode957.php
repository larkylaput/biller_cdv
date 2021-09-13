<?php

namespace App\Biller\Cdv\Validators;

use Throwable;
use Carbon\Carbon;
use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

// PayExpress
class BillerCode957 implements BillerCdvInterface {

    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
                $this->validateFormat($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) === 6) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateFormat($mainField) {
        $checkDigit = substr($mainField, 5, 1);
        $countX = 0;
        $productX = 0;
        $x =0;

        $formula['Account Number'] = $mainField;
        $formula['Last Digit'] = $checkDigit;


        while ($countX < 6) {
            $formula['Sum of X'][] = "$productX + " . substr($mainField, $countX, 1) . " = " . ($productX + substr($mainField, $countX, 1));
            $productX += substr($mainField, $countX, 1);
            $countX += 2;
        }

        $x = $productX * 3;
        $formula['Multiply of X'] = "$productX * 3 = $x";
        
        $countI = 1;
        $productI = 0;
        while ($countI < 5) {
            $formula['Sum of I'][] = "$productI + " . substr($mainField, $countI, 1) . " = " . ($productI + substr($mainField, $countI, 1));
            $productI += substr($mainField, $countI, 1);
            $countI += 2;
        }

        $y = $x + $productI;
        $formula['Sum of Y'] = "$x * $productI = $y";

        $z = 10 - fmod($y, 10);

        if ($z == 10) {
            $z = 0;
        }

        $formula['Equivalent of Z'] = "10 - ($y % 10) = $z";
        $formula['Check Digit'] = $checkDigit == $z;
        
        // dd($formula);

        return $checkDigit == $z;
    }

}
