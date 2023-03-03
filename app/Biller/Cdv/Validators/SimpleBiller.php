<?php

namespace App\Biller\Cdv\Validators;

class SimpleBiller
{
    public function validate($mainField, $regex): bool
    {
        return (bool)preg_match($regex, $mainField);
    }
}
