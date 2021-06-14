<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode44 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        
        try {
            // PNB Credit Cards
            // $mainField = preg_replace('/\D/', '', $mainField);
            if (
                $this->validateLength($mainField) and
                $this->validateNumbers($mainField) and
                $this->validateCharacters($mainField)
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
        if ($length <> 8) {
            return false;
        }
        return true;
    }

    private function validateNumbers($mainField) {
        return is_numeric($mainField);
    }

    private function validateCharacters($mainField){

        $strWeight = '';
        $strHead = '';
        $iTail = 0;
        $iTotal = 0;
        $nTotal = 0;
        $iIndex = intval(0);
        $iAmount = 0;
        $iNum1 = '';
        $iNum2 = '';
        $iDivisor = 0;
        $iRemainder = 0;
        $val1 = 0;
        $val2 = 0;
        $strWeight = intval('2468357');
        $strHead = substr($mainField, 0, 8);
        $iAccountLen = intval(8);  
        $iDivisor = 11;
        

        $mainField_lastdigit = substr($mainField,7,1); // should be 8,1 php starts with 0.
        $iTail = intval($mainField_lastdigit);

        
        while($iIndex < $iAccountLen){
            $val1 = substr($strHead,$iIndex,1);
            $val2 = intval(substr($strWeight,$iIndex,1));
            $iAmount = intval($val1*$val2);
            $iTotal = $iTotal + $iAmount;
            $iIndex++;
        }
       
        $iRemainder = (int)$iTotal % (int)$iDivisor;
     
        if($iRemainder == 0){
            return false; // 1

        }else if($iRemainder == 1){
            return true; // 0
        }else{
            $nTotal = $iDivisor - $iRemainder;
        }

        if($nTotal == $iTail){
            return true; // 0

        }else{
            return false; // 1
        }


    }

}
