<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

// Coca-Cola Beverages Philippines, Inc.
class BillerCode1068 implements BillerCdvInterface{

    const ALLOWED_NUMBER = [
        '0650','0010','0090','0100','0901','1010','0190','0400','0200','0300','0830','0890',
        '0670','0676','0671','0672','0673','0700','0710','0750','0730','0690','0960','0780',
        '0708','0765','0970','0770','0790','0704','0706','0720','0900','0908','0906','0909',
        '0910','0903','0911','0904','0912','0905','0920','0980','0990','0500','0501','0502',
        '0503','0504','0505','0506','0507','0508','0509','0510','0511','0512','0513','0514',
        '0515','0516','0517','0518','0519','0530','0531','0532','0533','0534','0535','0536',
        '0537','0538','0539','0540','0546','0547','0548','0549','0550','0551','0552','0553',
        '0554','0555','0556','0557','0558','0559','0560','0561','0562','0563','0564','0565',
        '0566','0567','0568','0569','0570','0571','0572','0573','0574','0575','0576','0577',
        '0578','0579','0580','0581','0582','0583','0584','0585','0586','0587','0588','0589',
        '0590','0591','0592','0593','0594','0595','0596','0597','0598','0599','0600','0620',
        '0609','0601','0602','0603','0604','0605','0606','0607','0608','0610','0611','0612',
        '0613','0614','0615','0616','0617','0618','0619','0626','0621','0622','0623','0624',
        '0625','0626','0628','0629','0631','0632','0633','0634','0635','0636','0637','0638',
        '0639','0640','0641','0642','0643','0644','0645','0646','0647','0648','0649','0651',
        '0652','0653','0654','0655','0656','0657','0658','0659','0661','0662','0663','0664',
        '0665','0667','0668','0681','0682', '0683'];

    const WEIGHT = [8,7,6,5,4,3,2];

    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
                $this->validatellowedFourDigitNumber($mainField) &&
                $this->validateMonthDay($mainField) &&
                $this->validateFormat($mainField)
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            throw new BillerValidatorException();
        }

        return false;
    }

    private function validateLength($mainField) {
        return (strlen($mainField) === 14) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validatellowedFourDigitNumber($mainField) {
        $fourDigit = substr($mainField, 0, 4);
        return in_array($fourDigit, Self::ALLOWED_NUMBER);
    }

    private function validateMonthDay($mainField) {
        $month = date('m');
        $day = date('d');
        if (
            substr($mainField, 10, 2) >= $month && 
            (substr($mainField, 12, 2) <= $day || substr($mainField, 12, 2) >= $day)
        ) { 
            return true;
        }

        return false;
    }

    private function validateFormat($mainField) {
        $sid1 = substr($mainField, 2, 1);
        $sid2 = substr($mainField, 3, 1);
        $sid3 = substr($mainField, 4, 1);
        $sid4 = substr($mainField, 5, 1);
        $sid5 = substr($mainField, 6, 1);
        $sid6 = substr($mainField, 7, 1);
        $sid7 = substr($mainField, 8, 1);
        $sid8 = substr($mainField, 9, 1);
        $sid9 = 0;

        $multiplier = [$sid1, $sid2, $sid3, $sid4, $sid5, $sid6, $sid7];

        for ($i=0; $i < count($multiplier); $i++) {
            $weight = Self::WEIGHT[$i];
            $multiply = $multiplier[$i] * $weight;
            $formula['Multiply'][] = "$multiplier[$i] * $weight = $multiply";

            $formula['Summation'][] = "$sid9 + $multiply = " . ($sid9 + $multiply);
            $sid9 += $multiply;
        }

        $smod = fmod($sid9, 11);
        $formula['smod'] = $smod;
        $sid9 = 11 - $smod;
        $formula['sid9'] = "11 - $smod = $sid9";

        if ($smod == 0) 
            return $smod == $sid8;
        else if ($sid9 == $sid8) 
            return true;

        return false;
    }
}
