<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

class BillerCode636 implements BillerCdvInterface
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
        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }

        return false;
    }

    public function validateLength($mainField) {
        $length = strlen($mainField);
        return ($length >= 7 && $length <= 11) ? true : false;
    }

    public function validateCharacters($mainField) {
        $prefix = substr($mainField, 0, 2);
        $array = ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17',
        '31','32','33','34','35',
        '37','38','39','40','41','42','43','44','45',
        '51','52','53','54','55','56',
        '61','62','63','64','65','66','67',
        '69','70','71',
        '81','82','83','84','85','86'];

        return in_array($prefix, $array) ? true : false;
    }
}
