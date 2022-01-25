<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode1142 implements BillerCdvInterface
{

    public function validate($mainField, $amount, $other_fields): bool
    {
        try {
            if (
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
                // $this->validateDueDate($other_fields['other_fields']['due_date']) &&
                $this->validateFormat($mainField, $amount)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        $length = strlen($mainField);
        return (in_array($length, [8,12])) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateDueDate($dueDate) {
        return (now() <= $dueDate) ? true : false;
    }

    private function validateFormat($mainField, $amount) {
        $length = strlen($mainField);

        if ($length == 12) {

            $mainField2 = substr($mainField, 0, 10);
            $checkDigit1 = substr($mainField, 10, 1);
            $checkDigit2 = substr($mainField, -1);

            $weight3 = ['11','10','9','8','7','6','5','4','3','2'];
            $weight4 = ['3','4','9','3','4','9','3','4','9','3'];

            $weight1 = ['11','10','9','8','7','6','5','4','3','2','1'];
            $new_mainField1 = substr($mainField,0,11);
            $weight2 = ['3','4','9','3','4','9','3','4','9','3','1'];
            $new_mainField2 = substr($mainField,0,10);

            return ($this->validateMainField($mainField, $new_mainField1, $new_mainField2, $weight1, $weight2) && $this->validateCheckDigit($mainField2, $checkDigit1, $checkDigit2, $weight3, $weight4));

        } else if ($length == 8) {
            $mainField1 = $mainField + $amount;
            $weight1 = ['7','6','5','4','3','2','1'];
            $new_mainField1 = substr($mainField1,0,7);
            $weight2 = ['3','4','9','3','4','9','1'];
            $new_mainField2 = substr($mainField1,0,6);

            $multiAdd = $amount * 100;
            $mainField2 = $mainField + $multiAdd;

            $new_mainField11 = substr($mainField2,0,7);
            $new_mainField22 = substr($mainField2,0,6);

            $checkDigit1 = substr($mainField2, 6, 1);

            $checkDigit2 = substr($mainField2, -1);
            $weight3 = ['7','6','5','4','3','2'];
            $weight4 = ['3','4','9','3','4','9'];

            if ($this->validateMainField($mainField1, $new_mainField1, $new_mainField2, $weight1, $weight2) == true) {
                return true;
            } else if ($this->validateMainField($mainField1, $new_mainField1, $new_mainField2, $weight1, $weight2) == false){
                return $this->validateMainField($mainField2, $new_mainField11, $new_mainField22, $weight1, $weight2) && $this->validateCheckDigit($mainField2, $checkDigit1, $checkDigit2, $weight3, $weight4);
            }
        }
    }

    private function validateMainField($mainField, $new_mainField1, $new_mainField2, $weight1, $weight2) {
        $sum1 = 0;

        $split_mainField1 = str_split($new_mainField1);

        foreach($weight1 as $key => $data){
            $product1 = $split_mainField1[$key] * $data;
            $sum1 += $product1;
        }

        $modulo1 = fmod($sum1, 10);

        if ($modulo1 == 0) {
            $sum2 = 0;
            $last_digit = substr($mainField, -1);
            $split_mainField2 = str_split($new_mainField2);
            $split_mainField2[] = $last_digit;

            foreach($weight2 as $key => $data){
                $product2 = $split_mainField2[$key] * $data;
                $sum2 += $product2;
            }

            $modulo2 = fmod($sum2, 10);

            return ($modulo2 == 0) ? true : false;
        }

        return false;
    }

    private function validateCheckDigit($mainField, $checkDigit1, $checkDigit2, $weight1, $weight2) {
        $sum1 = 0;

        $split_mainField = str_split($mainField);

        foreach($weight1 as $key => $data){
            $product1 = $split_mainField[$key] * $data;
            $sum1 += $product1;
        }

        $modulo1 = fmod($sum1, 10);
        $modulo1 = 10 - $modulo1;

        if ($modulo1 == $checkDigit1) {
            $sum2 = 0;
            $split_mainField = str_split($mainField);

            foreach($weight2 as $key => $data){
                $product2 = $split_mainField[$key] * $data;
                $sum2 += $product2;
            }

            $modulo2 = fmod($sum2, 10);
            $modulo2 = 10 - $modulo2;

            return ($modulo2 == $checkDigit2) ? true : false;
        }
    }
}
