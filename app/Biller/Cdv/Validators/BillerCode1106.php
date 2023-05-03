<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1106 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        $getDate = substr($mainField, 12, 8);
        $getAmount = $amount * 100;
        $getSumDA = $getDate + $getAmount;

        try {
            if (
                $this->validateLength($mainField) &&
                $this->validateCharacters($amount) &&
                $this->validateDueDate($mainField, $getSumDA) &&
                $this->validateCheckDigitAccountNumber($mainField) &&
                $this->validateCheckDigitDueDate($getSumDA)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) === 20) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateDueDate($mainField, $getSumDA) {
        $getDueDate = substr($getSumDA, 0, 6);
        $systemDate = date('ymd');

        return $systemDate <= $getDueDate;
    }

    private function validateCheckDigitAccountNumber($mainField) {
        $counter = 11;
        $position = 0;
        $product1 = 0;
        $sum1 = 0;
        $multiplier = 0;
        $product2 = 0;
        $sum2 = 0;

        for ($i=$counter; $i > 1; $i--) { 
            $substr = substr($mainField, $position, 1);
            $product1 = $i * $substr;
            $formula['product1'][] = "$i * $substr = $product1";

            $formula['sum1'][] = "$sum1 + $product1 = " . ($sum1 + $product1);
            $sum1 += $product1;

            if ($position == 0 || $position == 3 || $position == 6 || $position == 9) {
                $multiplier = 3;
            } else if ($position == 1 || $position == 4 || $position == 7) {
                $multiplier = 4;
            } else {
                $multiplier = 9;
            }

            $product2 = $multiplier * $substr;
            $formula['product2'][] = "$multiplier * $substr = $product2";

            $formula['sum2'][] = "$sum2 + $product2 = " . ($sum2 + $product2);
            $sum2 += $product2;

            $position++;
        }


        $remainder1 = fmod($sum1, 10);
        $formula['remainder1'] = "$sum1 % 10 = $remainder1";

        if ($remainder1 == 10) {
            $checkDigit1 = 0;
            $formula['checkDigit1'] = $checkDigit1;
        } else {
            $checkDigit1 = 10 - $remainder1;
            $formula['checkDigit1'] = "10 - $remainder1 = $checkDigit1";
        }

        $remainder2 = fmod($sum2, 10);
        $formula['remainder2'] = "$sum2 % 10 = $remainder2";

        if ($remainder2 == 0) {
            $checkDigit2 = 0;
            $formula['checkDigit2'] = $checkDigit2;
        } else {
            $checkDigit2 = 10 - $remainder2;
            $formula['checkDigit2'] = "10 - $remainder2 = $checkDigit2";
        }

        $lastDigit1 = substr($mainField, 10, 1);
        $lastDigit2 = substr($mainField, 11, 1);
        $formula['lastDigit1'] = $lastDigit1;
        $formula['lastDigit2'] = $lastDigit2;

        $formula['Check Digit'] = $checkDigit1 == $lastDigit1 && $checkDigit2 == $lastDigit2;
        // dd($formula);
        return $checkDigit1 == $lastDigit1 && $checkDigit2 == $lastDigit2;
    }

    private function validateCheckDigitDueDate($getSumDA) {
        $counter = 7;
        $position = 0;
        $product1 = 0;
        $sum1 = 0;
        $multiplier = 0;
        $product2 = 0;
        $sum2 = 0;

        for ($i=$counter; $i >= 1; $i--) { 
            $substr = substr($getSumDA, $position, 1);
            $product1 = $i * $substr;
            $formula['product1'][] = "$i * $substr = $product1";

            $formula['sum1'][] = "$sum1 + $product1 = " . ($sum1 + $product1);
            $sum1 += $product1;

            if ($position == 0 || $position == 3) {
                $multiplier = 3;
            } else if ($position == 1 || $position == 4) {
                $multiplier = 4;
            } else if ($position == 2 || $position == 5) {
                $multiplier = 9;
            } else {
                $multiplier = 1;
            }

            if ($i == 1) {
                $substr1 = substr($getSumDA, 7, 1);
                $product2 = $multiplier * $substr1;
                $formula['product2'][] = "($i) $multiplier * $substr1 = $product2";
            } else {
                $product2 = $multiplier * $substr;
                $formula['product2'][] = "($i) $multiplier * $substr = $product2";
            }

            $formula['sum2'][] = "$sum2 + $product2 = " . ($sum2 + $product2);
            $sum2 += $product2;

            $position++;
        }

        $remainder1 = fmod($sum1, 10);
        $formula['remainder1'] = "$sum1 % 10 = $remainder1";
        $remainder2 = fmod($sum2, 10);
        $formula['remainder2'] = "$sum2 % 10 = $remainder2";

        $formula['Check Digit'] = $remainder1 == 0 && $remainder2 == 0;
        // dd($formula);
        return$remainder1 == 0 && $remainder2 == 0;

    }
}
