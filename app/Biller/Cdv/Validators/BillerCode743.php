<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode743 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) && 
                $this->validateCharacters($mainField)
            ) {
                if ($this->validateNewAlgoFormat($mainField) || $this->validateOldAlgoFormat($mainField)) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) === 12) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateNewAlgoFormat($mainField) {
        $array = [60,88];
        if (in_array(substr($mainField, 0, 2), $array)) {
            $lastDigit = substr($mainField, -1);

            $formula['Account Number'] = $mainField;
            $formula['Last Digit'] = $lastDigit;

            $weight1 = '98765432';
            $indentCount1 =  0;
            $subCount1 = 3;
            $sum1 = 0;
            $product1 = 0;

            while ($indentCount1 < 8) {
                $multi1 = (int)substr($weight1, $indentCount1, 1);
                $multi2 = (int)substr($mainField, $subCount1, 1);
                $product1 = $multi1 * $multi2;
                $formula['Multiply Product 1'][] = "$multi1 * $multi2 = $product1";

                $formula['Sum Product 1'][] = "$sum1 + $product1 = " . ($sum1 + $product1);
                $sum1 += $product1;

                $indentCount1++;
                $subCount1++;
            }

            $weight2 = '121110';
            $indentCount2 =  0;
            $subCount2 = 0;
            $sum2 = 0;
            $product2 = 0;

            while ($subCount2 < 3) {
                $multi1 = (int)substr($weight2, $indentCount2, 2);
                $multi2 = (int)substr($mainField, $subCount2, 1);
                $product2 = $multi1 * $multi2;
                $formula['Multiply Product 2'][] = "$multi1 * $multi2 = $product2";

                $formula['Sum Product 2'][] = "$sum2 + $product2 = " . ($sum2 + $product2);
                $sum2 += $product2;

                $indentCount2 += 2;
                $subCount2++;
            }

            $totalSum = $sum1 + $sum2;
            $formula['Total Sum'][] = "$sum1 + $sum2 = $totalSum";

            $mod = fmod($totalSum, 10);
            $formula['Modulo'][] = "$totalSum % 10 = $mod";

            $checkDigit = 10 - $mod;
            if ($checkDigit == 10) {
                $checkDigit = 0;
            }

            $formula['Check Digit'] = "10 - $mod = $checkDigit";
            $formula['Difference'] = $checkDigit == $lastDigit;

            // dd($formula);

            return $checkDigit == $lastDigit;
        }

        return false;
    }

    private function validateOldAlgoFormat($mainField) {
        $twoDigit = substr($mainField, 0, 2);
        $threeDigit =  (int)substr($mainField, 8, 3);

        $firstCondition = [21, 22];

        if (
            (in_array($twoDigit, $firstCondition) && $threeDigit >= 1 && $threeDigit <= 999)
        ) {
            $lastDigit = substr($mainField, -1);

            $formula['Account Number'] = $mainField;
            $formula['Last Digit'] = $lastDigit;

            $count = 0;
            $sum = 0;
            $digitPosition = 12;

            while ($count < 11) {
                $multi = (int)substr($mainField, $count, 1);
                $temp = $multi * $digitPosition;
                $formula['Multiply'][] = "$multi * $digitPosition = $temp";

                $formula['Sum'][] = "$sum * $temp = " . ($sum + $temp);
                $sum += $temp;

                $count++;
                $digitPosition--;
            }

            $remainder = fmod($sum, 10);
            $formula['Modulo'][] = "$sum % 10 = $remainder";

            $checkDigit = 10 - $remainder;

            if ($checkDigit == 10) {
                $checkDigit = 0;
            }

            $formula['Check Digit'] = "10 - $remainder = $checkDigit";
            $formula['Difference'] = $checkDigit == $lastDigit;
            dd($formula);

            return $checkDigit == $lastDigit;;
        }

        return false;
    }
}
