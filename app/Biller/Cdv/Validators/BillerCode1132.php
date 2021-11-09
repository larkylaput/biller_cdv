<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1132 implements BillerCdvInterface
{
    const ACC_MULTIPLIER1 = [12,11,10,9,8,7,6,5,4,3,2,1];
    const ACC_MULTIPLIER2 = [3,4,9,3,4,9,3,4,9,3,4,1];
    const DDA_MULTIPLIER1 = [7,6,5,4,3,2,1];
    const DDA_MULTIPLIER2 = [3,4,9,3,4,9,1];

    public function validate($mainField, $amount): bool
    {
        $amount = $amount * 100;
        $secondSeq = substr($mainField, 13, 10);
        $dof = $secondSeq - $amount;

        try {
            if ($this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
                $this->validateDueDate($dof) &&
                $this->validateFormat($mainField, $dof)
            ) { 
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) == 23) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateDueDate($dof) {
        $getDueDate = substr($dof, 0, 6);
        $systemDate = date('ymd');

        return $systemDate <= $getDueDate;
    }

    private function validateFormat($mainField, $dof) {
        $actCount = 0;
        $actSum1 = 0;
        $actSum2 = 0;

        for ($i=$actCount; $i <= 11; $i++) { 
            $substr = substr($mainField, $i, 1);
            $actMultiplier1 = Self::ACC_MULTIPLIER1[$i];
            $actProduct1 = $substr * $actMultiplier1;
            $formula['actProduct1'][] = "$substr * $actMultiplier1 = $actProduct1";

            $actMultiplier2 = Self::ACC_MULTIPLIER2[$i];
            $substr2 = substr($mainField, 12, 1);

            if ($i == 11) {
                $actProduct2 = $substr2 * $actMultiplier2;
                $formula['actProduct2'][] = "$substr2 * $actMultiplier2 = $actProduct2";
            } else {
                $actProduct2 = $substr * $actMultiplier2;
                $formula['actProduct2'][] = "$substr * $actMultiplier2 = $actProduct2";
            }

            $formula['actSum1'][] = "$actSum1 + $actProduct1 = " . ($actSum1 + $actProduct1);
            $formula['actSum2'][] = "$actSum2 + $actProduct2 = " . ($actSum2 + $actProduct2);

            $actSum1 += $actProduct1;
            $actSum2 += $actProduct2;
        }

        $actRemainder1 = fmod($actSum1, 10);
        $formula['actRemainder1'] = "$actSum1 % 10 = $actRemainder1";

        $actRemainder2 = fmod($actSum2, 10);
        $formula['actRemainder2'] = "$actSum2 % 10 = $actRemainder2";

        if ($actRemainder1 == 0 && $actRemainder2 == 0) {
            return $this->validateDOF($dof);
        }
    }

    private function validateDOF($dof) {
        $dofCount = 0;
        $dofSum1 = 0;
        $dofSum2 = 0;

        $formula['dof'] = $dof;

        for ($i=$dofCount; $i <= 6; $i++) { 
            $substr = substr($dof, $i, 1);
            $dofMultiplier1 = Self::DDA_MULTIPLIER1[$i];
            $dofProduct1 = $substr * $dofMultiplier1;
            $formula['dofProduct1'][] = "$substr * $dofMultiplier1 = $dofProduct1";

            $dofMultiplier2 = Self::DDA_MULTIPLIER2[$i];
            $substr2 = substr($dof, 7, 1);

            if ($i == 6) {
                $dofProduct2 = $substr2 * $dofMultiplier2;
                $formula['dofProduct2'][] = "$substr2 * $dofMultiplier2 = $dofProduct2";
            } else {
                $dofProduct2 = $substr * $dofMultiplier2;
                $formula['dofProduct2'][] = "$substr * $dofMultiplier2 = $dofProduct2";
            }

            $formula['dofSum1'][] = "$dofSum1 + $dofProduct1 = " . ($dofSum1 + $dofProduct1);
            $formula['dofSum2'][] = "$dofSum2 + $dofProduct2 = " . ($dofSum2 + $dofProduct2);

            $dofSum1 += $dofProduct1;
            $dofSum2 += $dofProduct2;
        }

        $dofRemainder1 = fmod($dofSum1, 10);
        $formula['dofRemainder1'] = "$dofSum1 % 10 = $dofRemainder1";

        $dofRemainder2 = fmod($dofSum2, 10);
        $formula['dofRemainder2'] = "$dofSum2 % 10 = $dofRemainder2";

        return $dofRemainder1 == '0' && $dofRemainder2 == '0';
    }
}
