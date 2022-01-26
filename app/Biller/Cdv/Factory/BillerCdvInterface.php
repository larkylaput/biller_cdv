<?php

namespace App\Biller\Cdv\Factory;

interface BillerCdvInterface
{
    public function validate($mainField, $amount): bool;
}
