<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode37 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if(
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
                $this->validateAccountNumber($mainField) &&
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
        return (strlen($mainField) === 16) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateAccountNumber($mainField) {
        $array = [421434, 421562, 421665, 421679, 428303, 428307, 428339, 428346, 428353, 449174];
        
        $sixDigit = substr($mainField, 0, 6);
        $tenDigit = substr($mainField, 6, 10);

        if (
            (
                (in_array($sixDigit, $array)) || 
                (
                    $sixDigit >= 421562 && $sixDigit <= 421565 ||
                    $sixDigit >= 428307 && $sixDigit <= 428309 ||
                    $sixDigit >= 428346 && $sixDigit <= 428347 ||
                    $sixDigit >= 428353 && $sixDigit <= 428356
                )
            ) && 
            ($tenDigit >= '0000000000' && $tenDigit <= '9999999999')
        ) {
            return true;
        }

        return false;
    }

    private function validateFormat($mainField) {
        $lastDigit = substr($mainField, -1);

        $formula['Account Number'] = $mainField;
        $formula['Last Digit'] = $lastDigit;
        
        $product = 0;
        $sum = 0;
        $count = 0;

        while ($count < 15) {
            $multi1 = (int)substr($mainField, $count, 1);
            $multi2 = (int)fmod($count+1, 2) + 1;

            $product = $multi1 * $multi2;
            $formula['Multiply'][] = "$multi1 * $multi2 = $product";

            if ($product > 9) {
                $split = str_split($product);
                $product = $split[0] + $split[1];
                $formula['Sum More Than 9'][] = "$split[0] + $split[1] = $product";
            }

            $formula['Sum'][] = "$sum + $product = " . ($sum + $product);
            $sum += $product;
            
            $count++;
        }

        $mod = fmod($sum, 10);
        $formula['Modulo'][] = "$sum % 10 = $product";

        $checkDigit = 10 - $mod;
        $formula['Check Digit'][] = "10 - $mod = $checkDigit";

        if ($checkDigit == 10) {
            $checkDigit = 0;
        }

        $formula['difference'][] = $checkDigit == $lastDigit;

        // dd($formula);

        return $checkDigit == $lastDigit;
    }

}
