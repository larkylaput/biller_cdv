<?php

namespace App\Biller\Cdv\Validators;

use DateTime;
use Carbon\Carbon;
use App\Modules\Biller\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode807Old implements BillerCdvInterface
{
    const BILLER = 'MERALCO';
    private $daysOfMonth;
    private $year;
    private $currentDate;

    const WEIGHT1 = [10, 8, 7, 6, 5, 4, 3, 2, 1];
    const WEIGHT2 = [4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    const ADDDITIONAL_DAY = 13;

    public function __construct()
    {
        $this->currentDate = Carbon::now();
        $this->year = Date('Y');
        // $this->currentDate = Carbon::parse('1996-05-04');
        // $this->year = 1996;
        $this->daysOfMonth = [
            '01' => 31,
            '02' => $this->isLeapYear($this->year) ? 29 : 28,
            '03' => 31,
            '04' => 30,
            '05' => 31,
            '06' => 30,
            '07' => 31,
            '08' => 31,
            '09' => 30,
            '10' => 31,
            '11' => 30,
            '12' => 31
        ];
    }

    public function validate($mainField, $amount): bool
    {
        try {
            if (
                is_numeric($mainField) and
                $this->validateSequenceNumber($mainField) and
                $expiry = $this->validateBillDate($mainField) and
                $this->validateCheckDigitOne($mainField) and
                $this->validateCheckDigitTwo($mainField, $amount, $expiry)
            ) {
                return true;
            }
        } catch (Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }

    private function validateSequenceNumber($subscriberNumber)
    {
        $sequenceNo = substr($subscriberNumber, 0, 1);
        return in_array($sequenceNo, range(0, 8));
    }

    private function validateBillDate($subscriberNumber, $format = 'm-d')
    {

        $month = substr($subscriberNumber, 11, 2);
        $day = substr($subscriberNumber, 13, 2);
        $monthDay = $month . '-' . $day;

        if ($this->validMonth($month) and $this->validDay($month, $day)) {
            if ($expiryDate = $this->isNotExpired($month, $day)) {
                return $expiryDate;
            }
        }

        return false;
    }

    private function validateCheckDigitOne($subscriberNumber)
    {
        $serviceIdNo = str_split(substr($subscriberNumber, 1, 9));
        $inputedCheckDigit = intval(substr($subscriberNumber, 10, 1));
        $product = [];
        $sum = 0;

        foreach ($serviceIdNo as $key => $value) {
            $value *= Self::WEIGHT1[$key];
            $product[$key] = $value;
            $sum += $value;
        }
        $checkDigit = fmod($sum, 9) ?: 9;

        if ($checkDigit == $inputedCheckDigit) {

            return true;
        }
        return false;
    }

    private function validateCheckDigitTwo($subscriberNumber, $amount, Carbon $expiryDate)
    {
        $billDate = substr($subscriberNumber, 11, 4);
        $inputedCheckDigit = substr($subscriberNumber, 15, 1);
        $initial = substr($subscriberNumber, 0, 11);
        $total = str_pad(intval($initial) + intval($this->buildBillDate($expiryDate, $billDate)) + intval($amount *= 100), 11, "0", STR_PAD_LEFT);
        $characters = str_split($total);
        $sum = 0;
        $product = [];

        foreach ($characters as $key => $value) {
            $sum += $value * Self::WEIGHT2[$key];
            $product[$key] = "$value * ". Self::WEIGHT2[$key].' = '.$value * Self::WEIGHT2[$key];
        }

        $checkDigit = intval(fmod($sum, 11));

        if ($checkDigit == 0 or $checkDigit == 10) {
            $checkDigit = 0;
        }

        if ($checkDigit == $inputedCheckDigit) {
            return true;
        }
        return false;
    }

    private function buildBillDate(Carbon $expiryDate, $billDate)
    {

        $m = intval($expiryDate->format('m'));
        $d = intval($expiryDate->format('d'));
        if ($m == 1 and $d <= Self::ADDDITIONAL_DAY) {
            $expiryDate->subYear();
        }

        return $expiryDate->format('y').$billDate;
    }

    private function isLeapYear($year = null)
    {
        $year = $year ?: Date('Y');
        return DateTime::createFromFormat('Y', $year)->format('L') === "1";
    }

    private function isNotExpired($month, $day)
    {
        // $monthMaxDays = intval($this->daysOfMonth[$month]);
        // $year = $this->year;
        // $computedDay = $day + Self::ADDDITIONAL_DAY;

        // if ($computedDay > $monthMaxDays) {
        //     $computedDay = $computedDay - $monthMaxDays;
        //     $month += 1;
        //     if ($month > 12) {
        //         $month = 1;
        //         $year += 1;
        //     }
        // }
        
        // $expiryDate = Carbon::createFromFormat('Y-m-d H:i:s', "$year-$month-$computedDay 23:59:59");
        
        $expiryDate = Carbon::createFromFormat('Y-m-d', "{$this->year}-$month-$day")->addDays(Self::ADDDITIONAL_DAY);
            
        // dd(
        //     'current = '.$this->currentDate,
        //     'expiry = '.$expiryDate,
        //     $expiryDate->greaterThan($this->currentDate) ? 'Valid' : 'Invalid'
        // );

        if ($expiryDate->greaterThan($this->currentDate)) {
            return $expiryDate;
        }
        return false;
    }

    private function validMonth($month)
    {
        // check if in range of number of months
        return in_array($month, array_keys($this->daysOfMonth));
    }

    private function validDay($month, $day)
    {
        // check if in range of month's number of days
        if ($day > 0 and $day <= $this->daysOfMonth[$month]) {
            return true;
        }

        return false;
    }
}
