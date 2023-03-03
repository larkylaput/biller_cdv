<?php

namespace App\Biller\Cdv\Validators;

use App\Exceptions\BillerValidatorException;
use App\Biller\Cdv\Factory\BillerCdvInterface;

class BillerCode1126 implements BillerCdvInterface
{
    public function validate($mainField, $amount): bool
    {
        // dd($this->validateFormat($mainField, $amount));
        try {
            if(
                $this->validateLength($mainField) &&
                $this->validateCharacters($mainField) &&
                $this->validateFormat($mainField, $amount)
            ) {
                return true;
            }

        } catch (\Throwable $th) {
            throw new BillerValidatorException();
        }
        return false;
    }
    
    private function validateLength($mainField) {
        return (strlen($mainField) === 23) ? true : false;
    }

    private function validateCharacters($mainField) {
        return is_numeric($mainField);
    }

    private function validateFormat($mainField, $amount) {
        $check_digit_multiplier1 = '13131313131';
        $check_digit_multiplier2 = '34934934934';
        $date_amount_multiplier1 = '1313131';
        $date_amount_multiplier2 = '3493491';
        $discounted_date = date('ymd', strtotime('-1 day', strtotime(now())));
        $amount = round($amount, 2);
        $amount *= 100;
        $check_digit_amount_reference_number = (int)substr($mainField, 15, 8);
        $sum_date_amount = $check_digit_amount_reference_number + $amount;
        $due_date_time = substr($sum_date_amount, 0, 6) . '1400';
        $pay_date_time = date('ymdhi');

        $formula['Account Number'] = $mainField;

        $check_digit_counter = 0;
        $check_digit_product1 = 0;
        $check_digit_product2 = 0;
        $check_digit_sum1 = 0;
        $check_digit_sum2 = 0;

        while ($check_digit_counter <= 10) {
            $product1_number1 = (int)substr($mainField, $check_digit_counter, 1);
            $product2_number2 = (int)substr($check_digit_multiplier1, $check_digit_counter, 1);

            $check_digit_product1 = $product1_number1 * $product2_number2;
            $formula['Product 1'][] = "$product1_number1 X $product2_number2 = $check_digit_product1";

            if ($check_digit_product1 >= 10) {
                $product1 = (int)substr($check_digit_product1, 0, 1);
                $product2 = (int)substr($check_digit_product1, 1, 1);

                $formula['Product 1 final'][] = "($check_digit_product1) $product1 + $product2 = " . ($product1 + $product2);
                $check_digit_product1 = $product1 + $product2;
            }

            $product2_number1 = (int)substr($mainField, $check_digit_counter, 1);
            $product2_number2 = (int)substr($check_digit_multiplier2, $check_digit_counter, 1);

            $check_digit_product2 = $product2_number1 * $product2_number2;
            $formula['Product 2'][] = "$product2_number1 X $product2_number2 = $check_digit_product2";
            
            $formula['Summation 1'][] = "($check_digit_product1) $check_digit_sum1 + $check_digit_product1 = " . ($check_digit_sum1 + $check_digit_product1);
            $check_digit_sum1 += $check_digit_product1;

            $formula['Summation 2'][] = "($check_digit_product2) $check_digit_sum2 + $check_digit_product2 = " . ($check_digit_sum2 + $check_digit_product2);
            $check_digit_sum2 += $check_digit_product2;

            $check_digit_counter++;
        }

        $check_digit_remainder1 = (int)fmod($check_digit_sum1, 10);
        $check_digit_remainder2 = (int)fmod($check_digit_sum2, 10);

        $formula['Check 1'][] = "Modulo: $check_digit_sum1 % 10 = $check_digit_remainder1";
        $formula['Check 2'][] = "Modulo: $check_digit_sum2 % 10 = $check_digit_remainder2";

        $check_digit_computed1 = 10 - $check_digit_remainder1;
        $check_digit_computed2 = 10 - $check_digit_remainder2;

        $check_digit1 = $check_digit_computed1;
        $check_digit2 = $check_digit_computed2;

        if ($check_digit_computed1 === 10) 
            $check_digit1 = 0;

        if ($check_digit_computed2 === 10) 
            $check_digit2 = 0;


        $formula['Check 1'][] = "Checker: 10 - $check_digit_remainder1 = $check_digit1";
        $formula['Check 2'][] = "Checker: 10 - $check_digit_remainder2 = $check_digit2";

        $formula['Check 1'][] = "Check digit : " . substr($mainField, 11, 1);
        $formula['Check 2'][] = "Check digit : " . substr($mainField, 12, 1);

        $formula['Check 1'][] = $check_digit1===(int)substr($mainField, 11, 1);
        $formula['Check 2'][] = $check_digit2===(int)substr($mainField, 12, 1);

        if ($check_digit1 === (int)substr($mainField, 11, 1) && $check_digit2 === (int)substr($mainField, 12, 1)) {
            $date_amount_counter = 0;
            $date_amount_product1 = 0;
            $date_amount_product2 = 0;
            $date_amount_sum1 = 0;
            $date_amount_sum2 = 0;
            
            $formula['check_digit_amount_reference_number'][] = $check_digit_amount_reference_number;
            $formula['amount'][] = $amount;
            $formula['sum_date_amount'][] = $sum_date_amount;

            while ($date_amount_counter <= 6) {
                $date_amount_product1_number1 = (int)substr($sum_date_amount, $date_amount_counter, 1);
                $date_amount_product1_number2 = (int)substr($date_amount_multiplier1, $date_amount_counter, 1);

                $date_amount_product1 = $date_amount_product1_number1 * $date_amount_product1_number2;
                $formula['Date Amount 1'][] = "$date_amount_product1_number1 X $date_amount_product1_number2 = $date_amount_product1";

                if ($date_amount_product1 >= 10) {
                    $amount_product1 = (int)substr($date_amount_product1, 0, 1);
                    $amount_product2 = (int)substr($date_amount_product1, 1, 1);

                    $formula['Date Amount 1 final'][] = "($date_amount_product1) $amount_product1 + $amount_product2 = " . ($amount_product1 + $amount_product2);
                    $date_amount_product1 = $amount_product1 + $amount_product2;
                }

                if ($date_amount_counter == 6) {
                    $date_amount_product2_number1 = (int)substr($sum_date_amount, 7, 1);
                    $date_amount_product2_number2 = (int)substr($date_amount_multiplier2, $date_amount_counter, 1);

                    $date_amount_product2 = $date_amount_product2_number1 * $date_amount_product2_number2;
                } else {
                    $date_amount_product2_number1 = (int)substr($sum_date_amount, $date_amount_counter, 1);
                    $date_amount_product2_number2 = (int)substr($date_amount_multiplier2, $date_amount_counter, 1);

                    $date_amount_product2 = $date_amount_product2_number1 * $date_amount_product2_number2;
                }

                $formula['Date Amount 2'][] = "$date_amount_product2_number1 X $date_amount_product2_number2 = $date_amount_product2";

                $formula['Date Amount Summation 1'][] = "($date_amount_product1) $date_amount_sum1 + $date_amount_product1 = " . ($date_amount_sum1 + $date_amount_product1);
                $date_amount_sum1 += $date_amount_product1;

                $formula['Date Amount Summation 2'][] = "($date_amount_product2) $date_amount_sum2 + $date_amount_product2 = " . ($date_amount_sum2 + $date_amount_product2);
                $date_amount_sum2 += $date_amount_product2;

                $date_amount_counter++;
            }

            $date_amount_remainder1 = (int)fmod($date_amount_sum1, 10);
            $date_amount_remainder2 = (int)fmod($date_amount_sum2, 10);

            $formula['Date Amount Check 1'][] = "Modulo: $date_amount_sum1 % 10 = $date_amount_remainder1";
            $formula['Date Amount Check 2'][] = "Modulo: $date_amount_sum2 % 10 = $date_amount_remainder2";

            $formula['Date Amount Check 1'][] = 0===$date_amount_remainder1;
            $formula['Date Amount Check 2'][] = 0===$date_amount_remainder2;
            // return $formula;
            $formula['Last Data'][] = "$pay_date_time <= $due_date_time";
            $formula['Last Data'][] = $pay_date_time <= $due_date_time;

            // return $formula;

            if ($date_amount_remainder1 === 0 && $date_amount_remainder2 === 0) {
                if ($pay_date_time <= $due_date_time)
                    return true;
                else
                    return false;
            } else {
                return false;
            }
        }


        return false;
    }
}
