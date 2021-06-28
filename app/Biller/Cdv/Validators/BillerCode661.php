<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;


class BillerCode661 implements BillerCdvInterface {
    const VALID_LENGTH = [14, 16];
    const SIXTEEN_DIGITS_ALLOWED_INITIAL_DIGITS = [
        518217,
        518178,
        515603,
        531210,
        549832,
        542551,
        525617,
        510186
    ];

    const FOURTEEN_DIGITS_ALLOWED_INITIAL_DIGITS = [
        '30',
        '36',
        '38',
        '39'
    ];
    const WEIGHT = 212121212121212;
    private $mainField;
    // Security Bank Mastercard
    public function validate($mainField, $amount): bool{
        $this->mainField = $mainField;
        try {
            // dd(
            //     (bool) $len = $this->getLength(), $this->validateField($len)
            // );
            return (bool) $len = $this->getLength($mainField) and $this->validateField($len);

        } catch (Throwable $th) {
            throw $th;
            throw new BillerValidatorException();
        }
        return false;
    }


    private function getLength(){
       return in_array(strlen($this->mainField), self::VALID_LENGTH) ? strlen($this->mainField) : false;
    }

    private function validateField($len)
    {
        if ($len == 16 and is_numeric($this->mainField)) {
            return $this->validateSixteenDigits();
        }

        if ($len == 14) {
            return $this->validateFourteenDigits();
        }

        return false;
    }

    private function validateSixteenDigits()
    {
        if (!in_array(substr($this->mainField, 0, 6), self::SIXTEEN_DIGITS_ALLOWED_INITIAL_DIGITS)) {
            return false;
        }

        $count = 0;
        $test = [];
        $total = 0;
        $checkDigit = 0;
        $chk = substr($this->mainField, -1, 1);

        while ($count <= 15) {
            $fieldValue = intval(substr($this->mainField, $count, 1));
            $weightValue = intval(substr(self::WEIGHT, $count, 1));
            $product = $fieldValue * $weightValue;
            if ($product > 9) {
                $num1 = substr($product, 0, 1);
                $num2 = substr($product, 1, 1);
                $sum = intval($num1) + intval($num1);
                $total += $sum;
            } else {
                $total += $product;
            }
            $count += 1;
        } 
        
        $checkDigit = $this->checkDigit(15, $checkDigit, $total);
      
        return $checkDigit == $chk;
    }

    private function validateFourteenDigits()
    {
        if (!in_array(substr($this->mainField, 0, 6), self::FOURTEEN_DIGITS_ALLOWED_INITIAL_DIGITS)) {
            return false;
        }

        $checkDigit = $this->checkDigit(13, $checkDigit, $total);
      
        return $checkDigit == $chk;
    }

    private function checkDigit($loopCount, $checkDigit, $total)
    {
        $count = 0;
        $test = [];
        $total = 0;
        $checkDigit = 0;
        $chk = substr($this->mainField, -1, 1);

        while ($count <= $loopCount) {
            $fieldValue = intval(substr($this->mainField, $count, 1));
            $weightValue = intval(substr(self::WEIGHT, $count, 1));
            $product = $fieldValue * $weightValue;
            $test[$count] = "$fieldValue * $weightValue = $product";
            if ($product > 9) {
                $num1 = substr($product, 0, 1);
                $num2 = substr($product, 1, 1);
                $sum = intval($num1) + intval($num1);
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
