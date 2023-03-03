<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1133 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        // dd($this->validateDates($mainField));
        try {
            if (
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
                $this->validateDates($mainField) &&
                $this->validateAmount($mainField, $amount) &&
                $this->validateFormat($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) >= 1 && strlen($mainField) <= 24) ? true : false;
    }

    private function validateCharacters($mainField) {
        return ctype_alnum($mainField);
    }

    private function validateDates($mainField) {
        $due_date = (int)substr($mainField, 8, 6);
        $current_date = (int)date('mdy');
        $ddyy = (int)substr($mainField, 12, 2);
        $ccyy = (int)date('y');

        return $ddyy >= $ccyy && $due_date >= $current_date;
    }

    private function validateAmount($mainField, $amount) {
        $amount = $amount * 100;
        $amount = explode('.', $amount);
        $snamount = (int)substr($mainField, 14, strlen($mainField) - 16);

        return $amount[0] == $snamount;
    }

    private function validateFormat($mainField) {
        $counter = 0;
        $reverse_mainField = strrev(substr($mainField, 0, strlen($mainField) - 1));
        $sum = 0;
        $computation = 0;
        $remainder = 0;
        $check_digit = 0;

        if (substr($mainField, strlen($mainField)- 2, 1) == '1' || substr($mainField, strlen($mainField) - 2, 1) == '2') {
            while ($counter <= strlen($reverse_mainField) - 1) {
                $ref_char = substr($reverse_mainField, $counter, 1);

                if (ctype_alpha($ref_char) ) {
                    $ref_char = ord($ref_char) - 48;
                    $ref_char = (string)$ref_char;
                }

                $formula['ref char'][] = $ref_char;

                if ((int)fmod($counter, 2) == 1) {
                    $formula['summer'][] = "(counter:$counter) ref_char:$ref_char : $sum + $ref_char = " . ($sum + $ref_char);                    
                    $sum += (int)$ref_char;
                } else {
                    $computation = ((2 * (int)$ref_char)) - (9 * ((int)$ref_char / 5));
                    $formula['computation'][] = "($computation) ref_char:$ref_char : (2 * $ref_char) - (9 * ($ref_char / 5)) = ". ((2 * (int)$ref_char) - (9 * ((int)$ref_char / 5)));

                    if (is_float($computation)) {
                        $explode = explode('.', $computation);
                        $computation = $explode[0] + $explode[1];
                    }
                    $formula['float'][] = "($computation) : $explode[0] + $explode[1] = ". ($explode[0] + $explode[1]);

                    $formula['summer'][] = "(counter:$counter) computation:$computation : $sum + $computation = " . ($sum + $computation);
                    $sum += $computation;
                }

                $formula['sum'][] = $sum;
                $counter++;
            }
            $formula['total'][] = $sum;

            // $formula['total'][] = round($sum);

            // $sum = round($sum);

            $remainder = (int)fmod($sum, 10);

            $formula['remainder'][] = "$sum % 10 = " . fmod($sum, 10);


            if ($remainder > 0) {
                $check_digit = 10 - $remainder;
            }

            $formula['check_digit'][] = "($check_digit) : 10 - $remainder = " . ((10 - $remainder));

            // return $formula;
            return $check_digit == substr($mainField, -1);
        }

        return false;
    }
}
