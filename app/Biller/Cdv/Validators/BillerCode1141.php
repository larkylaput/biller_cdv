<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode1141 implements BillerCdvInterface
{

    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) && 
                $this->validateCharacters($mainField)
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

    private function validateCharacters($mainField) {
        $year = [date('Y'), date('Y') - 1];

        if (substr($mainField, 0, 4) == 'HRTP') {
            if (substr($mainField, 4, 1) == '-') {
                if (in_array(substr($mainField, 5, 4), $year)) {
                    if (substr($mainField, 9, 1) == '-') {
                        if (is_numeric(substr($mainField, 10, 5))) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}
