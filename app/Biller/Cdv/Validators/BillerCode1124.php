<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1124 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if (
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
        return ($length >= 5 && $length <= 10) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateDigit($mainField) {
        return $mainField >= 10006;
    }

    private function validateFormat($mainField) {
        $length = strlen($mainField);
        $position = 0;
        $total = 0;

        for ($i=$length; $i >= 1; $i--) { 
            $substr = substr($mainField, $position, 1);
            $product = $i * $substr;
            $formula['product'][] = "$i * $substr = $product";

            $formula['total'][] = "$total * $product = " . ($total + $product);
            $total += $product;

            $position++;
        }

        $remainder = fmod($total, 11);
        $formula['remainder'] = "$total % 11 = $remainder"; 
        $formula['check digit'] = $remainder == 10 || $remainder == 0; 
        // dd($formula);

        return $remainder == 10 || $remainder == 0;
    }
}
