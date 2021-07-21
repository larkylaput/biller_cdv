<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode264 implements BillerCdvInterface
{
    const CODE = ['248','267','264','263','265'];
    
    // Eton Residence Greenbelt Condominium Corporation
    public function validate($mainField, $amount): bool
    {
        // 264070001904 
        // 264070031506
        // 264070031506
        // 264070031506
        // 264070031506

        // dd($this->validateFormat($mainField));
        try {
            if(
                $this->validateCode($mainField) AND
                $this->validateLength($mainField) AND
                $this->validateCharacters($mainField) AND
                $this->validateFirst3Digits($mainField) AND
                $this->validateFormat($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    public function validateCode($mainField) {
        $first3digit = substr($mainField, 0, 3);
        return array_search($first3digit, Self::CODE);
    }

    public function validateLength($mainField) {
        return strlen($mainField) === 12 ? true : false;
    }
    
    public function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    public function validateFirst3Digits($mainField) {
        $first3digit = substr($mainField, 0, 3);
        $result = false;
        foreach (Self::CODE as $code) {
            if ($first3digit === $code)
                $result = true;
        }

        return $result;
    }

    public function validateFormat($mainField) {
        $count = 1;
        $sum = 0;
        
        for ($i=$count; $i <= 10; $i++) { 
            $product = 0;
            $multi = 2 - fmod($count, 2);
            $multi = (int)$multi;

            $product = (int)substr($mainField, $count, 1) * $multi;
            $product = (int)$product;

            if (strlen($product) === 2)
                $product = (int)substr($product, 1, 1) + (int)substr($product, 2, 1);

            $sum = $sum + $product;
            $count = $count + 1;
        }

        $cdv = 10 - fmod($sum, 10);
        $cdv = (int)$cdv;

        if ($cdv === 10)
            $cdv = 0;

        if ((int)substr($mainField, -2) <> $cdv)
            return false;

        return true;
    }
}
