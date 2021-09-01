<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1001 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        // dd($this->validateCharacters($mainField));
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
        return (strlen($mainField) >= 11 && strlen($mainField) <= 30) ? true : false;
    }

    private function validateCharacters($mainField) {
        return ctype_alnum($mainField);
    }

    // private function validateLength($mainField) {
    //     return (strlen($mainField) === 11 || strlen($mainField) === 14) ? true : false;
    // }

    // private function validateCharacters($mainField) {
    //     if (strlen($mainField) === 11) {
    //         $string = substr($mainField, 0, 5);
    //         $number = substr($mainField, 5, 6);
    //         $array_string = ['BLCEB','BLMNL','BLDVO','BLCGY','BLGES','BLOZA','BLTAC'];

    //         if (in_array(strtoupper($string), $array_string) && is_numeric($number))
    //             return true;

    //     } else if (strlen($mainField) === 14) {
    //         $first_string = substr($mainField, 0, 3);
    //         $second_string = substr($mainField, 3, 3);
    //         $third_string = substr($mainField, 6, 8);
    //         $fourth_string = substr($mainField, 10, 2);
    //         $fifth_string = substr($mainField, 12, 2);
    //         $sixth_string = substr($mainField, 6, 4);

    //         // return $sixth_string;

    //         $first_array = ['01','03','05','07','08','10','12']; 
    //         $second_array = ['04','06','09','11']; 

    //         if (strtoupper($first_string) === 'SOA' && 
    //             !is_numeric($second_string) &&
    //             is_numeric($third_string)
    //         ) {
    //             if (in_array($fourth_string, $first_array)) {
    //                 if ($fifth_string > 0 && $fifth_string < 32) {
    //                     return true;
    //                 }
    //             } else if (in_array($fourth_string, $second_array)) {
    //                 if ($fifth_string > 0 && $fifth_string < 31) {
    //                     return true;
    //                 }
    //             } else if ($fourth_string === '02') {
    //                 if ((int)fmod($sixth_string, 4) === 0) {
    //                     if ($fifth_string > 0 && $fifth_string < 30) {
    //                         return true;
    //                     }
    //                 }
    //             } else {
    //                 if ($fifth_string > 0 && $fifth_string < 29) {
    //                     return true;
    //                 }
    //             }
    //         } 
    //     }

    //     return false;
    // }

}