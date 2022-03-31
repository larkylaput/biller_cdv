<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode136 implements BillerCdvInterface
{
    const WEIGHT1 = [3,2,1];
    const WEIGHT2 = [6,5,4];
    const WEIGHT3 = [0, 1, 2, 3, 4, 5, 6, 7, 8];

    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
                $this->validateCheckDigitForAmount($mainField, $amount)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) === 16) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateCheckDigitForAmount($mainField, $amount) {
        $amount = str_split($amount);
        $sequenceNumber = substr($mainField, 7, 8);
        $checkDigit = substr($mainField, -1);
        $count = count($amount);

        $checkAmount = substr($sequenceNumber, -2);
        $computed1 = $this->computeAmount(self::WEIGHT1, $amount, $count);
        $computed2 = $this->computeAmount(self::WEIGHT2, $amount, $count);

        if($checkAmount == $computed1.$computed2) {
            return $this->computeCheckDigit($sequenceNumber.$count, $checkDigit);
        }
        return false;
    }

    private function computeAmount($weight, $amount, $count) {
        $product = 0;
        $sum = 0;
        foreach ($amount as $key => $value) {
            if ($count == 3)
                $product = $weight[$key] * $value;
            else if ($count == 2)
                $product = $weight[$key+1] * $value;
            else if ($count == 1)
                $product = $weight[$key+2] * $value;

            $sum += $product;
        }

        $remainder = fmod($sum, 10);
        $nextHighNum = ($sum - $remainder) + 10;
        $computed = $nextHighNum - $sum;
        if ($computed == 10) $computed = 0;

        return $computed;
    }

    private function computeCheckDigit($combineNumber, $checkDigit) {
        $product = 0;
        $sum = 0;
        foreach (self::WEIGHT3 as $key => $value) {
            $product = $value * $combineNumber[$key];
            $sum += $product;
        }

        $remainder = fmod($sum, 10);
        $nextHighNum = ($sum - $remainder) + 10;
        $computed = $nextHighNum - $sum;

        return $computed == $checkDigit;
    }
}
