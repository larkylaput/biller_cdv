<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode10 implements BillerCdvInterface
{
    const WEIGHT1 = [2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2];
    const WEIGHT2 = [4, 3, 2, 7, 6, 5, 4, 3, 2];
    const WEIGHT3 = [1, 2, 3, 1, 2, 3, 1, 2, 3];

    public function validate($mainField, $amount): bool {
        try {
            if (
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField)
            ) {
                if (
                    $this->validateBin($mainField) &&
                    $this->validateCheckDigit($mainField, 1)   
                ) {
                    return true; 
                } else {

                    if ($this->validateCheckDigit($mainField, 2)) {
                        return true;
                    } else {
                        if ($this->validateCheckDigit($mainField, 3)) {
                            return true;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        return strlen($mainField) == 14 ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateBin($mainField) {
        $first6 = substr($mainField, 0, 6);
        $bin = ['840018', '871018', '874018', '882018', '890018', '901018', 
                '841018', '872018', '880018', '884018', '891018', '910018',
                '870018', '873018', '881018', '885018', '900018', '911018', 
                '000076', '000078'
                ];

        if (in_array($first6, $bin)) {
            return true;
        }

        return false;
    }

    private function validateCheckDigit($mainField, $algo) {

        $strlen = strlen($mainField) - 1;
        $checkDigit = substr($mainField, $strlen, 1);

        if ($algo == 1) {
            $accountNumber = str_split(substr($mainField, 0, $strlen));
        } else if ($algo == 2 || $algo == 3) {
            $accountNumber = str_split(substr($mainField, 4, 9));
        }

        $formula['Account Number'] = $mainField;
        $formula['Account Number Split'] = $accountNumber;
        $formula['Check Digit'] = $checkDigit;
        
        $product = 0;
        $sum = 0;

        foreach ($accountNumber AS $key => $value) {
            if ($algo == 1) {
                $product = $value * Self::WEIGHT1[$key];
                $formula['Product'][] = "$value X ".Self::WEIGHT1[$key]. " = $product";

                if($product > 9) {
                    $result = str_split($product);
                    foreach ($result AS $i => $val) {
                        $formula['Summation'][] = "($product) $sum + $val = " . ($sum + $val);
                        $sum += $val;
                    }
                } else {
                    $formula['Summation'][] = "($product) $sum + $product = " . ($sum + $product);
                    $sum += $product;
                }
            } else if ($algo == 2) {
                $product = $value * Self::WEIGHT2[$key];
                $formula['Product'][] = "$value X ".Self::WEIGHT2[$key]. " = $product";

                $formula['Summation'][] = "($product) $sum + $product = " . ($sum + $product);
                $sum += $product;
            }
            else if ($algo == 3) {
                $product = $value * Self::WEIGHT3[$key];
                $formula['Product'][] = "$value X ".Self::WEIGHT3[$key]. " = $product";

                $formula['Summation'][] = "($product) $sum + $product = " . ($sum + $product);
                $sum += $product;
            }
        }

        if ($algo == 1) {
            $diff = fmod($sum, 10);
            $nextHighNum = ($sum - $diff) + 10;
            $computed = $nextHighNum - $sum;

            if ($diff == 0) {
                $computed = 0;
            }

            $formula['Check'][] = "Next Higher Number: $nextHighNum";
            $formula['Check'][] = "Checker: $nextHighNum - $sum = $computed";
        } else if ($algo == 2) {
            $diff = fmod($sum, 11);
            $computed = 11 - $diff;

            if ($computed >= 10) {
                $computed = 11 - $computed;
            }

            $formula['Check'][] = "Modulo: $sum % 11 = " . (fmod($sum, 11));
            $formula['Check'][] = "Checker: 11 - $diff = $computed";
        } else if ($algo == 3) {
            $diff = fmod($sum, 11);
            $computed = 11 - $diff;

            if ($diff == 0 || $diff == 1) {
                $computed = 0;
            }
        }
        
        $formula['Check'][] = $checkDigit==$computed;

        // dd($formula);
        return $checkDigit == $computed;
    }
}
