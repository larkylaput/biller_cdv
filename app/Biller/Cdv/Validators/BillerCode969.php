<?php

namespace App\Biller\Cdv\Validators;

use Throwable;
use Carbon\Carbon;
use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

// PayExpress
class BillerCode969 implements BillerCdvInterface {
    private $mainField;
    private $fholdC;
    private $sholdC;
    private $tholdC;
    private $dHCode;
    private $dYCode;
    private $mMVCHold;
    private $mMCHold;
    private $mTCHold;
    private $tWHC;
    private $tTHC;
    private $foTHC;
    private $fiTHC;
    private $sTHC;
    const GET_YEAR = 2016;
    const HD_CODE_VAL_1 = 1156;
    const HD_CODE_VAL_2 = 34;
    const HT_CODE_VAL = 2399;
    const F_AMOUNT = self::HD_CODE_VAL_1 * self::HD_CODE_VAL_2;
    const S_AMOUNT = self::F_AMOUNT * self::HD_CODE_VAL_2;
    
    public function validate($mainField, $amount): bool
    {
        $this->setVariables($mainField, $amount);

        try {
            return $this->validateField();

        } catch (Throwable $th) {
            throw $th;
            throw new BillerValidatorException();
        }
    }

    private function validateField()
    {
        if (strlen($this->mainField) != 16 or preg_match('%[^a-zA-Z0-9]%', $this->mainField)) {
             return false;
        }
     
        $ySum = intval(intval($this->dYCode) + intval(self::GET_YEAR));
 
        if (in_array(intval($this->dHCode), range(1, 31))) {
            if ($ySum == date('Y')) {
                $vC = intval(self::HD_CODE_VAL_1 * intval($this->mMVCHold) + intval($this->mMCHold) * self::HD_CODE_VAL_2 + intval($this->mTCHold));
                $mC = intval($vC/self::HT_CODE_VAL + 1);
                $tC = intval($vC - ($mC - 1) * self::HT_CODE_VAL);
                $tAmount = number_format(((self::S_AMOUNT * intval($this->tWHC)) + (self::F_AMOUNT * intval($this->tTHC)) + (self::HD_CODE_VAL_1 * intval($this->foTHC)) + (self::HD_CODE_VAL_2 * intval($this->fiTHC)) +  intval($this->sTHC))/100, 2, '.', '');

                if (in_array($mC, range(1, 12))) {
                    $ComDate = str_pad($mC, 2, '0', STR_PAD_LEFT)."/$this->dHCode/$ySum";
                }

                $exConDate = Carbon::Parse($ComDate)->addDays(2)->format('Ymd');
                if ($tAmount == $this->amount) {
                    return $exConDate >= Carbon::now()->format('Ymd');
                }
            }
        }
     
        return false;
    }

    private function setVariables($mainField, $amount)
    {
        $this->mainField = $mainField;
        $this->amount = $amount;
        $this->fholdC =  $this->parseCharacter(substr($this->mainField, 0,1));
        $this->sholdC = $this->parseCharacter(substr($this->mainField, 1,1));
        $this->tholdC = $this->parseCharacter(substr($this->mainField, 2,1));
        $this->dHCode = $this->parseCharacter2(substr($this->mainField, 3,1));
        $this->dYCode = $this->parseCharacter(substr($this->mainField, 4,1));
        $this->mMVCHold = $this->parseCharacter(substr($this->mainField, 5,1));
        $this->mMCHold = $this->parseCharacter(substr($this->mainField, 6,1));
        $this->mTCHold = $this->parseCharacter(substr($this->mainField, 7,1));
        $this->tWHC = $this->parseCharacter(substr($this->mainField, 11,1));
        $this->tTHC = $this->parseCharacter(substr($this->mainField, 12,1));
        $this->foTHC = $this->parseCharacter(substr($this->mainField, 13,1));
        $this->fiTHC = $this->parseCharacter(substr($this->mainField, 14,1));
        $this->sTHC = $this->parseCharacter(substr($this->mainField, 15,1));
    }

    private function parseCharacter($char)
    {
        if (is_numeric($char)) {
             return intval($char);
        }

        $ascii = ord($char);
        if ($ascii - 64 <= 11) {
            return $ascii - 55;
        }

        if ($ascii - 64 >= 15) { 
            return $ascii - 57;
        }

        return $ascii - 56;
    }

    private function parseCharacter2($char)
    {
        if (is_numeric($char)) {
             return intval($char) + 25;
        }

        $ascii = ord($char);
        if ($ascii - 64 <= 11) {
            return $ascii - 64;
        }

        if ($ascii - 64 >= 15) {
            return $ascii - 66;
        }

        return $ascii - 65;
    }
}
