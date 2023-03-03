<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

// Security Bank Mastercard
class BillerCode661 implements BillerCdvInterface {
    const VALID_LENGTH = [14, 16];
    const SIXTEEN_DIGITS_ALLOWED_INITIAL_DIGITS = [
        518217,
        518178, 
        515603, 
        531210, 
        549832, 
        525617,
        542551, 
        542594, 
        510186
    ];

    const WEIGHT = 212121212121212;
    private $mainField;
    
    public function validate($mainField, $amount): bool
    {
        $this->mainField = $mainField;
        try {
            return $this->validateField();

        } catch (Throwable $th) {
            throw $th;
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateField()
    {
        $len = strlen($this->mainField);
        if ($len == 16 and is_numeric($this->mainField)) {
            return $this->validateCheckDigit();
        }

        return false;
    }

    private function validateCheckDigit()
    {
        if (!in_array(substr($this->mainField, 0, 6), self::SIXTEEN_DIGITS_ALLOWED_INITIAL_DIGITS)) {
            return false;
        }

        $check = substr($this->mainField, -1);
        $checkDigit = $this->computeCheckDigit($check);
        dd($checkDigit);
        return $checkDigit == $check;
    }

    private function computeCheckDigit($checkDigit)
    {
        $count = 0;
        $total = 0;
        
        while ($count <= 14) {
            $fieldValue = intval(substr($this->mainField, $count, 1));
            $weightValue = intval(substr(self::WEIGHT, $count, 1));
            $product = $fieldValue * $weightValue;
            if ($product > 9) {
                $num1 = substr($product, 0, 1);
                $num2 = substr($product, 1, 1);
                $sum = intval($num1) + intval($num2);
                $total += $sum;
            } else {
                $total += $product;
            }
            $count += 1;
        } 
        
        if (($total % 10) != 0){
            $tmp = $total;
            WHILE ($tmp % 10 <> 0) {
                $tmp = $tmp + 1;
            }
            return $tmp - $total;
        }

        return $checkDigit;
    }
}
    