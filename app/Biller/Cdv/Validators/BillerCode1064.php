<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1064 implements BillerCdvInterface
{
    const WGHT1 = '1212121212';

    // 266120055009  
    // 266120069405 
    // 266120034103 
    // 266120020403
    // 266120000906

    public function validate($mainField, $amount): bool
    {
        // dd($this->validateFormat($mainField));
        try {
            if (
                $this->validateLength($mainField) AND
                $this->validateFirst3Digit($mainField) AND
                $this->validateCharacters($mainField) AND
                $this->validateFormat($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }

        return false;
    }

    public function validateLength($mailField) {
        return (strlen($mailField) === 12) ? true : false;
    }

    public function validateFirst3Digit($mailField) {
        return (substr($mailField, 0, 3) === '266') ? true : false;
    }

    public function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    public function validateFormat($mainField) {
        $Sum1 = 0;

        for ($IndCnt=1; $IndCnt < 10; $IndCnt++) { 
            $Prdt1 = (int)substr(Self::WGHT1, $IndCnt , 1) * (int)substr($mainField, $IndCnt , 1);

            if (strlen($Prdt1) === 2) {
                $Pos1 = (int)substr($Prdt1, 1 , 1) + (int)substr($Prdt1, 2 , 1);
            } else if (strlen($Prdt1) === 1) {
                $Pos1 = (int)$Prdt1;
            }

            $Sum1 = $Sum1 + $Pos1;
        }

        $Mod1 = fmod($Sum1, 10);
        $Chk = 10 - $Mod1;

        $Chk = (strlen($Chk) === 2) ? substr(substr($Chk, 0, 3) , 2, 1) : $Chk;
        $Chk = (int)$Chk;

        if ($Chk === (int)substr($mainField , 11)) {
            return true;
        }

        return false;
    }
}
