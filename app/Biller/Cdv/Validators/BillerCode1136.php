<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1136 implements BillerCdvInterface
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
        $month = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];

        if (ctype_alpha(substr($mainField, 0, 1))) {
            if (is_numeric(substr($mainField, 1, 6))) {
                if (substr($mainField, -1) === 'F') {
                    if (substr($mainField, 7, 2) >= 01 && substr($mainField, 7, 2) <= 31) {
                        if (substr($mainField, 12, 2) >= 01 && substr($mainField, 12, 2) <= 99) {
                            if (in_array(substr($mainField, 9, 3), $month)) {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }
}
