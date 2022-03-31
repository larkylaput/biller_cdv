<?php

namespace App\Biller\Cdv\Validators;

use Throwable;
use Carbon\Carbon;
use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode26 implements BillerCdvInterface
{
    CONST WEIGHT = [1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1];

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

    private function validateLength($mainField)
    {
        return (strlen($mainField) == 16) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateFormat($mainField) {
        $cardNumber = str_split(substr($mainField, 0, 15));
        $checkDigit = substr($mainField, -1);

        $evenProduct = [];
        $oddSum = 0;
        foreach ($cardNumber as $key => $value) {
            if ($key % 2 == 0) {
                $evenProduct[] = $value + Self::WEIGHT[$key];
            } else {
                $oddSum += $value;
            }
        }

        $evenSum = $this->convertMulEntry($evenProduct);

        $totalSum = $evenSum + $oddSum;

        $diff = fmod($totalSum, 10);
        $nextHighNum = ($totalSum - $diff) + 10;
        $computed = $nextHighNum - $totalSum;
        if ($diff == 0) {
            $computed = 0;
        }

        return $computed == $checkDigit;
    }

    public function convertMulEntry($evenProduct) {
        $convertProduct = [];
        foreach ($evenProduct as $value) {
            switch ($value) {
                case 1:
                    $convertProduct[] = "00";
                    break;
                case 2:
                    $convertProduct[] = "02";
                    break;
                case 3:
                    $convertProduct[] = "04";
                    break;
                case 4:
                    $convertProduct[] = "06";
                    break;
                case 5:
                    $convertProduct[] = "08";
                    break;
                case 6:
                    $convertProduct[] = "01";
                    break;
                case 7:
                    $convertProduct[] = "03";
                    break;
                case 8:
                    $convertProduct[] = "05";
                    break;
                case 9:
                    $convertProduct[] = "07";
                    break;
                case 10:
                    $convertProduct[] = "09";
                    break;
            }
        }

        $finalProduct = implode("", $convertProduct);

        return $this->sumDigit(str_split($finalProduct));
    }

    public function sumDigit($finalProduct) {
        $sum = 0;
        foreach ($finalProduct as $value) {
            $sum += $value;
        }

        return $sum;
    }
}
