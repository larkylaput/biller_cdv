<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;
use Throwable;

// Coca-Cola Beverages Philippines, Inc.
class BillerCode1066 implements BillerCdvInterface{

    const WEIGHT1 = '987656543';
    const WEIGHT2 = '876545432';
    const WEIGHT3 = '6598764321';
    const ALLOWED_INITIAL_DIGITS = [
    'GMMN','VCCS','VPKS','MDDS','MGIS','GMMU','GMYU','NNCU','NICU','NCTU',
    'MZIN','SMPU','VCPU','VLOU','MDIU','MZIU','LAGR','NUEV','BICO','PANA',
    'MDTR','GMYN','NNCN','NICN','NCTN','SBLN','VNBN','VCON','VLTN','MDTN',
    'MCBK','GLRR','NPBR','NNSR','NIBR','SBBR','SBNR','VNDR','VCMR','MCBR',
    'VPID','MZDR','GLMK','NPCK','NCCK','NCSK','SBPK','SQLK','VNTK','VPIK',
    'VCCW','MGCK','MZOK','GAAD','NPUD','NCUD','NCBD','SBCD','SQSD','VCCD',
    'SMPS','MCMD','MGGD','MZZD','GBDW','NPRW','NISW','NCZW','SBTW','SMPW',
    'SBLU','VPKW','MDDW','MGIW','GMMS','GMYS','NNCS','NICS','NCTS','SBLS',
    'GLRN','VCPS','VLOS','MDIS','MZIS','GLRU','NPBU','NNSU','NIBU','SBBU',
    'MZDN','VNBU','VCOU','VLTU','MDTU','MZDU','ANTI','CAGA','QUEZ','LEYT',
    'MGCR','NPBN','NNSN','NIBN','SBBN','SBNN','VNDN','VCMN','MCBN','MGCN',
    'MCMK','GLMR','NPCR','NCCR','NCSR','SBPR','SQLR','VNTR','VPIR','MCMR',
    'VPKD','MZOR','GAAK','NPUK','NCUK','NCBK','SBCK','SQSK','VCCK','VPKK',
    'VCPW','MGGK','MZZK','GBDD','NPRD','NISD','NCZD','SBTD','SMPD','VCPD',
    'VNBS','MDDD','MGID','GMMW','GMYW','NNCW','NICW','NCTW','SBLW','VNBW',
    'SBNU','VLOW','MDIW','MZIW','GLRS','NPBS','NNSS','NIBS','SBBS','SBNS',
    'GLMN','VCOS','VLTS','MDTS','MZDS','GLMU','NPCU','NCCU','NCSU','SBPU',
    'MZON','VNDU','VCMU','MCBU','MGCU','MZOU','BICU','ILOC','MIMA','CAGY',
    'MGGR','NPCN','NCCN','NCSN','SBPN','SQLN','VNTN','VPIN','MCMN','MGGN',
    'MDDK','GAAR','NPUR','NCUR','NCBR','SBCR','SQSR','VCCR','VPKR','MDDR',
    'VLOD','MZZR','GBDK','NPRK','NISK','NCZK','SBTK','SMPK','VCPK','VLOK',
    'VCOW','MGIK','GMMD','GMYD','NNCD','NICD','NCTD','SBLD','VNBD','VCOD',
    'VNDS','MDID','MZID','GLRW','NPBW','NNSW','NIBW','SBBW','SBNW','VNDW',
    'SQLU','VLTW','MDTW','MZDW','GLMS','NPCS','NCCS','NCSS','SBPS','SQLS',
    'GAAN','VCMS','MCBS','MGCS','MZOS','GAAU','NPUU','NCUU','NCBU','SBCU',
    'MZZN','VNTU','VPIU','MCMU','MGGU','MZZU','MEYC','CENT','NEGR','DAVA',
    'MGIR','NPUN','NCUN','NCBN','SBCN','SQSN','VCCN','VPKN','MDDN','MGIN',
    'MDIK','GBDR','NPRR','NISR','NCZR','SBTR','SMPR','VCPR','VLOR','MDIR',
    'VLTD','GMMK','GMYK','NNCK','NICK','NCTK','SBLK','VNBK','VCOK','VLTK',
    'VCMW','MZIK','GLRD','NPBD','NNSD','NIBD','SBBD','SBND','VNDD','VCMD',
    'VNTS','MDTD','MZDD','GLMW','NPCW','NCCW','NCSW','SBPW','SQLW','VNTW',
    'SQSU','MCBW','MGCW','MZOW','GAAS','NPUS','NCUS','NCBS','SBCS','SQSS',
    'GBDN','VPIS','MCMS','MGGS','MZZS','GBDU','NPRU','NISU','NCZU','SBTU',
    'GMMR','VCCU','VPKU','MDDU','MGIU','MEND','PANG','BATA','CEBU','GENS',
    'MZIR','NPRN','NISN','NCZN','SBTN','SMPN','VCPN','VLON','MDIN','MDTK',
    'GMYR','NNCR','NICR','NCTR','SBLR','VNBR','VCOR','VLTR','MCBD','GLRK',
    'NPBK','NNSK','NIBK','SBBK','SBNK','VNDK','VCMK','VPIW','MZDK','GLMD',
    'NPCD','NCCD','NCSD','SBPD','SQLD','VNTD','MGCD','MZOD','GAAW','NPUW',
    'NCUW','NCBW','SBCW','SQSW','B050','MCMW','MGGW','MZZW','GBDS','NPRS',
    'NISS','NCZS','SBTS','ZAMB'];

    private $mainField;
    private $amount;
    public function validate($mainField, $amount): bool{
        $this->mainField = $mainField;
        $this->amount = $amount * 100;
        try {
            return $this->validateMainField();
        } catch (Throwable $th) {
            throw $th;
        }
    }

    private function validateMainField(){
        if (strlen($this->mainField) == 20) {
            return $this->validateTwentyDigits();
        }


        if (strlen($this->mainField) == 13) {
            return $this->validateThriteenDigits();
        }

        return false;
    }

    private function validateTwentyDigits()
    {        
        if ($this->calculateCheckOne() == substr($this->mainField, 9,1)) {
            $this->subnoCount = 10;
            $sum2 = $this->calculateWeightTwo();
            $sum3 = $this->calculateWeightThree();

            $sum4 = $sum2 + $sum3;
            $mod2 = $sum4 % 11;
            $check2 = ($mod2 * 9) % 10;

            return $check2 == substr($this->mainField, 19, 1) ? true : false;
        }
        return false;
    }

    private function calculateCheckOne()
    {
        $indCount = 0;
        $sum = 0;
        $test = [];
        while ($indCount < 10) {
            $product1 = $wv1 = intval(substr(self::WEIGHT1, $indCount, 1)) * $fv1 = intval(substr($this->mainField, $indCount, 1));
            $sum += $product1;
            $indCount += 1;
            $test[$indCount] = "$wv1 * $fv1 = $product1";
        }
        
        $mod = $sum % 11;
        return ($mod * 9) % 10;
    }

    private function calculateWeightTwo()
    {
        $sum = 0;
        $indCount = 0;
        $subnoCount = 10;

        while ($indCount < 20) {
            $product = intval(substr(self::WEIGHT2, $indCount, 1)) * intval(substr($this->mainField, $subnoCount, 1));
            $sum += $product;
            $indCount += 1;
            $subnoCount += 1;
        }

        return $sum;
    }

    private function calculateWeightThree()
    {
        $indCount = 0;
        $sum = 0;

        while ($indCount < 10) {
            $product = intval(substr(self::WEIGHT3, $indCount, 1)) * intval(substr(substr(str_repeat("0",10) . $this->amount, -10), $indCount, 1));
            $sum += $product;
            $indCount += 1;
        }

        return $sum;
    }

    private function validateThriteenDigits()
    {
        $firstFourDigits = substr($this->mainField, 0, 4);
        if (is_numeric(substr($this->mainField, 4, 9)) and in_array($firstFourDigits, self::ALLOWED_INITIAL_DIGITS)) {
            return true;
        }

        return false;
    }
}
