<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1044 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        try {
            if (
                $this->validateLength($mainField) && 
                $this->validateCharacter($mainField) &&
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

    private function validateCharacter($mainField) {
        $string = substr($mainField, 6, 8);
        return is_numeric($string);
    }

    private function validateFormat($mainField) {
        $firstThree = substr($mainField, 0, 3);
        $fourthThree = substr($mainField, 3, 3);

        if ($firstThree == 'ARV') {
            $array = ['BON','CPR','DUL','MOH','QUE','SCR','SDO','SFI','SJS','SNO','SOO','SSU','YUD'];
            if (in_array($fourthThree, $array))
                return true;
        } else if ($firstThree == 'CIT') {
            $array = ['ARS','BAY','BLU','BTA','CON','DAN','EDG','FLO','GEN','GLO','HIP','IND','JBA',
                        'JWI','KAH','KAU','LEG','LRT','MAB','MAL','MCL','MSY','MUE','NON','ORT','OSM',
                        'PAG','PRE','RES','RIB','RIM','ROS','ROX','RPA','RPB','SAG','SFE','SJO','TAN',
                        'TTA','TTB','VET','VIL','YUA','ZAM'];
            if (in_array($fourthThree, $array))
                return true;
        } else if ($firstThree == 'JAR') {
            $array = ['ARG','BAG','BEL','BEN','BGO','BIT','BTG','BUH','BUN','CAM','CBH','CUA','CUB',
                        'DEM','DES','DUA','DUB','EL9','FAJ','JAV','LAN','LOP','LUJ','MVH','MAH','MAR',
                        'MON','OFA','OLO','QUI','SEM','SID','SIM','SJE','SPE','SRO','SVI','TAC','TAG',
                        'TAY','TBL','UNG'];
            if (in_array($fourthThree, $array)) 
                return true;
        } else if ($firstThree == 'LAZ') {
            $array = ['AGU','BAN','BDZ','BTD','BUR','CAI','DIV','GUS','HIN','ING','JER','LAG','LJN',
                        'LJS','LUL','MAC','MAG','MDL','NAB','RAI','RIZ','SIS','SNI','TBJ','TIC'];
            if (in_array($fourthThree, $array)) 
                return true;
        } else if ($firstThree == 'LUZ') {
            $array = ['ALA','DON','JES','LIB','LNO','LOB','LSU','MAN','OBR','PRO','PUN','SIN'];
            if (in_array($fourthThree, $array)) 
                return true;
        } else if ($firstThree == 'MAN') {
            $array = ['ABE','AIR','BAK','BOL','BTN','BUC','CJN','DUC','GUZ','HNO','HSU','NAV','ONA',
                        'PAL','PHA','PHB','SRA','SRS'];
            if (in_array($fourthThree, $array)) 
                return true;
        } else if ($firstThree == 'MOL') {
            $array = ['COC','COM','CPG','EBA','ETI','HAB','INF','KAS','KAT','MOL','NBA','NFU','NSA',
                        'NVA','POB','SAN','SAT','SBA','SFU','SJU','SSA','TAA','TAP','WHA','WTI'];
            if (in_array($fourthThree, $array)) 
                return true;
        }

        return false;
    }
}
