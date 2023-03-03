<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

// Security Bank Mastercard
class BillerCode647 implements BillerCdvInterface {
    const WEIGHT_F = 313131;
    const WEIGHT_S = 3131313;
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
        
        if (
            strlen($this->mainField) != 15 or
            // !preg_match('/[^0-9]/', $this->mainField)
            !is_numeric($this->mainField)
        ) {
            false;
        }

        return $this->checkDigit();
    }

    private function checkDigit()
    {
        $totalF = $this->computeTotalF();


        $totalS = $this->computeTotalS();
        if (intval(substr($this->mainField, 13, 1)) == $totalF AND intval(substr($this->mainField, 14, 1)) == $totalS) {
            return true;
        }
        return false;
    }

    private function computeTotalF(){
        $count = 0;
        $sum = 0;

        while ($count <= 6) {
            $fieldValue = intval(substr($this->mainField, $count, 1));
            $weightValue = intval(substr(self::WEIGHT_F, $count, 1));
            $product =  $fieldValue * $weightValue;
            // echo "$product =  $fieldValue * $weightValue \n";
            $sum += $product;
            $count += 1;
        }

        $subtotal = $sum % 10;
        if ($subtotal == 0) {
            return 0;
        } 
            
        return 10 - $subtotal;
    }

    private function computeTotalS()
    {
        $subNo = substr($this->mainField, 6, 7);
        $count = 0;
        $sum = 0;

        while ($count <= 7) {
            $fieldValue = intval(substr($subNo, $count, 1));
            $weightValue = intval(substr(self::WEIGHT_S, $count, 1));
            $product = $fieldValue * $weightValue;
            // echo "$product = $fieldValue * $weightValue; \n";
            $sum = $sum + $product;
            $count = $count + 1;
        }
        
        $subtotal = $sum % 10;
        if ($subtotal == 0) {
            return 0;
        } 

        return 10 - $subtotal;
    }
}
