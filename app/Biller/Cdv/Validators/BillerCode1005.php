<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1005 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) &&
                $this->validateCharacter($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) === 10) ? true : false;
    }

    private function validateCharacter($mainField) {
        $area = substr($mainField, 3, 2);
        $location = substr($mainField, 0, 3);
        $salesman = substr($mainField, 5, 5);

        $location_code = 'OZA';
        $salesman_code = 'VSM' . $area;

        if (strtoupper($location) === $location_code &&
            is_numeric($area) &&
            strtoupper($salesman) === $salesman_code) 
        {
            return true;
        }

        return false;
    }
}