<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1023 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) &&
                $this->validateFirstToSevenCharacters($mainField) &&
                $this->validateLastCharacters($mainField) &&
                $this->validateYear($mainField) &&
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
        return (strlen($mainField) === 15) ? true : false;
    }

    private function validateFirstToSevenCharacters($mainField) {
        $number = substr($mainField, 0, 7);
        return is_numeric($number);
    }

    private function validateLastCharacters($mainField) {
        $last = substr($mainField, -1);
        return ($last === "C") ? true : false;
    }

    private function validateYear($mainField) {
        $year = substr($mainField, 12, 2);
        return ($year == date("y") || $year == date("y") - 1) ? true : false;
    }

    private function validateFormat($mainField) {
        $seven_two = substr($mainField, 7, 2);
        $nine_three = substr($mainField, 9, 3);

        $month1 = ['JAN','MAR','JUL','AUG','MAY','OCT','DEC'];
        $number1 = ['08','15','23','30','31'];
        $month2 = ['APR','JUN','SEP','NOV'];
        $number2 = ['08','15','23','30'];
        $number3 = ['08','15','23','28','29'];
        $number4 = ['08','15','23','28'];

        if (
                ( in_array($seven_two, $number1) && in_array($nine_three, $month1) ) ||
                ( in_array($seven_two, $number2) && in_array($nine_three, $month2) ) ||
                ( ($nine_three === "FEB" ) && in_array($seven_two, $number3) && ((int)fmod(date("Y"), 4) === 0) ) ||
                ( in_array($seven_two, $number4) )
        ) {
            return true;
        }

        return false;
    }

}
