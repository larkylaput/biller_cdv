<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1011 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        // dd($this->validateThirdFormat($mainField));
        try {
            // $mainField = preg_replace('/\D/', '', $mainField);
            if ($this->validateLength($mainField) &&
                $this->validateFirstFormat($mainField) &&
                $this->validateSecondFormat($mainField) &&
                $this->validateThirdFormat($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) === 15) ? true : false;
    }

    private function validateFirstFormat ($mainField) {
        $temp1 = substr($mainField, 0, 7);
        return is_numeric($temp1);
    }

    private function validateSecondFormat ($mainField) {
        $temp2 = substr($mainField, 7, 2);
        $temp3 = substr($mainField, 9, 3);
        $temp4 = substr($mainField, 12, 2);

        $array_number1 = ['08','15','23','30', '31'];
        $array_month1 = ['JAN','MAR','MAY','JUL', 'AUG', 'OCT', 'DEC'];
        $array_number2 = ['08','15','23','30'];
        $array_month2 = ['APR','JUN','SEP','NOV'];
        $array_number3 = ['08','15','23','28','29'];
        $array_month3 = ['FEB'];
        $array_number4 = ['08','15','23','28'];

        if (
                (!in_array($temp2, $array_number1) && in_array($temp3, $array_month1)) ||
                (!in_array($temp2, $array_number2) && in_array($temp3, $array_month2)) ||
                (!in_array($temp2, $array_number3) && in_array($temp3, $array_month3) && checkdate('02', '29', '20' . $temp4) === false) ||
                (!in_array($temp2, $array_number4) && in_array($temp3, $array_month3) && checkdate('02', '29', '20' . $temp4) === true)
            ) {
            return false;
        }

        return true;
    }

    private function validateThirdFormat($mainField) {
        $temp3 = substr($mainField, 9, 3);
        $temp4 = substr($mainField, 12, 2);
        $temp5 = substr($mainField, -1);

        $array_month4 = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

        if (
            !in_array($temp3, $array_month4) ||
            !is_numeric($temp4) ||
            $temp5 <> "U"
        ) {
            return false;
        }

        return true;
    }
}
