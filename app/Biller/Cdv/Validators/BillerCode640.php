<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode640 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            // PNB Credit Cards
            // $mainField = preg_replace('/\D/', '', $mainField); // remove all the non numberic characters
            if(
                $this->validateLength($mainField) and
                $this->validateCharacters($mainField) and
                $this->validateNumberic($mainField)
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
        $length = strlen($mainField);
        if ($length <> 10) {
            return false;
        }
        return true;
    }

    private function validateNumberic($mainField) {
        return is_numeric($mainField);
    }

    private function validateCharacters($mainField){

        $Weights = '987654321';
        $I = 0;
        $WP = 0;
        $WD = 0;
        $WR = 0;
        $WSS = 0;
        $R = 0;
        $LastDigit = 0;
        $WA = 0;

        $X = $mainField;
        $I = intval(0); // first digit of mainfield
        $P = intval(9); // last digit of mainfield

        while($I < $P){
            $WP = intval(substr($Weights,$I,1)) * intval(substr($X,$I,1));
            $WD = $WP / 10;
            $WR = fmod($WP,10);
            $WSS = $WD + $WR;

            if($WSS / 10 <> 0){
                $WD = $WSS / 10;
                $WR = fmod($WSS,10);
                $WSS = $WD + $WR;
            }

            $WA = $WA + $WSS;
            $I = $I + 1;
        }

        $R = fmod($WA,10);
        if($R > 0){
            $LastDigit = 10 - $R;
        }else{
            $LastDigit = 0;
        }

        if(intval(substr($X,9,1)) == $LastDigit){
            return true;
        }else{
            return false;
        }
    }
}
