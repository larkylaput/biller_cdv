<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1127 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if(
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
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
        return (strlen($mainField) === 11) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

   private function validateFormat($mainField) {
       $counter = 0;
       $sum = 0;
       $sum1 = 0;
       $sum2 = 0;
       $lastDigit = substr($mainField, -1);
       $test = [1,0,1,0,1,0,1,0,1,0];

       for ($i=$counter; $i <= 9; $i++) {
           $formula['test'][] = $test[$i];
           $substr = substr($mainField, $i, 1);

           if ($test[$i] == 0) {
               $vproduct = $substr * 2;
               $formula['vproduct'][] = "$substr * 2 = $vproduct";

               if (strlen($vproduct) > 1) {
                   $substr2 = substr($vproduct, 0, 1);
                   $substr3 = substr($vproduct, 1, 1);

                   $vproduct = $substr2 + $substr3;
                   $formula['vproduct'][] = "$substr2 + $substr3 = $vproduct";

               }
               $formula['sum2'][] = "$sum2 + $vproduct = " . ($sum2 + $vproduct);

               $sum2 += $vproduct;
           } else {
               $formula['sum1'][] = "$sum1 + $substr = " . ($sum1 + $substr);
               $sum1 += $substr;
           }
       }
       
       $sum = $sum1 + $sum2;
       $remainder = fmod($sum, 10);
       $formula['remainder'] = "($sum1 + $sum2 = $sum) % $remainder";

       $checkDigit = 0;

       if ($remainder > 0) {
           $checkDigit = 10 - $remainder;
       }

       $formula['final'] = "10 - $remainder = $checkDigit";
       $formula['checkDigit'] = $checkDigit == $lastDigit;
    //    dd($formula);

       return $checkDigit == $lastDigit;

   }
}
