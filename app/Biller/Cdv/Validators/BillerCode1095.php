<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1095 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
                $this->validateDates($mainField) &&
                $this->validateFormat($mainField, $amount)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) == 20) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateDates($mainField) {
        $month = substr($mainField, 8,2);
        $day = substr($mainField, 10,2);
        $year = substr($mainField, 6, 2);

        if (
            ($day == '02' && in_array($month, ['29', '30'])) ||
            ($month == '31' && in_array($day, ['02', '04', '06', '09', '11'])) &&
            ($year == date('y'))
        ) {
            return false;
        }

        if ($month.$day.$year >= date('mdy')) {
            return true;
        }

        return false;
    }

    private function validateFormat($mainField, $amount) {
        $split1 = str_split($mainField);
        $weight1 = [4,5,2,3,7,2,5,2,6,8];
        $checkWeight1 = [$split1[12], 
                        $split1[13], 
                        $split1[12], 
                        $split1[13], 
                        $split1[14],
                        $split1[15],
                        $split1[16], 
                        $split1[17],
                        $split1[18],
                        $split1[19]];

        $checkDigit1 = $split1[0];
        $checkDigit2 = $split1[1];

        $sum1 = 0;
        
        for ($i=0; $i < count($checkWeight1); $i++) { 
            $mul1 = $weight1[$i];
            $mul2 = $checkWeight1[$i];

            $multiply1 = $mul2 * $mul1;
            $formula['multiply1'][] = "$mul2 * $mul1 = $multiply1";

            $formula['sum1'][] = "$sum1 + $multiply1 = " . ($sum1 + $multiply1);

            $sum1 += $multiply1;
        }

        $remainder1 = fmod($sum1, 7);
        $formula['remainder1'] = "$sum1 % 7 = $remainder1";

        $formula['checkfirst'] = $remainder1 == $checkDigit1 && $checkDigit2 == '2';

        if ($remainder1 == $checkDigit1 && $checkDigit2 == '2') {
            $amount = $amount * 100;
            $amount = substr('00000000000' . $amount, -11);
            $weight2 = [6,8,6,2,5,2,7,3,2,5,4]; 
            $split2 = str_split($amount);
            $checkWeight2 = [$split2[10], 
                            $split2[9], 
                            $split2[8], 
                            $split2[7], 
                            $split2[6],
                            $split2[5],
                            $split2[4], 
                            $split2[3],
                            $split2[2],
                            $split2[1],
                            $split2[0]];
            
            $checkDigit3 = $split1[2];
            $checkDigit4 = $split1[5];
            
            $sum2 = 0;
        
            for ($i=0; $i < count($checkWeight2); $i++) { 
                $mul1 = $weight2[$i];
                $mul2 = $checkWeight2[$i];

                $multiply2 = $mul2 * $mul1;
                $formula['multiply2'][] = "$mul2 * $mul1 = $multiply2";

                $formula['sum2'][] = "$sum2 + $multiply2 = " . ($sum2 + $multiply2);

                $sum2 += $multiply2;
            }

            $remainder2 = fmod($sum2, 7);
            $formula['remainder2'] = "$sum2 % 7 = $remainder2";

            $formula['checksecond'] = $remainder2 == $checkDigit3 && $checkDigit4 == '1';
            if ($remainder2 == $checkDigit3 && $checkDigit4 == '1') {
                $weight3 = [4,5,2,3,7,2];
                $checkWeight3 = [$split1[6], 
                        $split1[7], 
                        $split1[8], 
                        $split1[9], 
                        $split1[10],
                        $split1[11]];

                $checkDigit5 = $split1[3];
                $checkDigit6 = $split1[4];
                $checkDigit7 = $split1[5];

                $sum3 = 0;

                for ($i=0; $i < count($checkWeight3); $i++) { 
                    $mul1 = $weight3[$i];
                    $mul2 = $checkWeight3[$i];
    
                    $multiply3 = $mul2 * $mul1;
                    $formula['multiply3'][] = "$mul2 * $mul1 = $multiply3";
    
                    $formula['sum3'][] = "$sum3 + $multiply3 = " . ($sum3 + $multiply3);
    
                    $sum3 += $multiply3;
                }

                $formula['sum3 + 5'] = "$sum3 + 5 = " . ($sum3 + 5);
                $sum3 += 5;

                $sumAll = $sum3 + $sum2 + $sum1 + 2;
                $formula['sumAll'] = "$sum3 + $sum2 + $sum1 + 2 = $sumAll";

                $remainder3 = fmod($sumAll, 7);
                $formula['remainder3'] = "$sumAll % 7 = $remainder3";

                $remainder4 = fmod($sum3, 7);
                $formula['remainder4'] = "$sum3 % 7 = $remainder4";

                $formula['checkthird'] = $checkDigit5 == $remainder3 && $checkDigit6 == $remainder4 && $checkDigit7 == '1';

                if ($checkDigit5 == $remainder3 && 
                    $checkDigit6 == $remainder4 && 
                    $checkDigit7 == '1') {
                    return true;
                }
            }
        }
        // dd($formula);

        return false;
    }

}