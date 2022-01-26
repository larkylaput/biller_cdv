<?php

namespace App\Biller\Cdv\Factory;

class BillerCdv
{
    private $validator;

    public function __construct(BillerCdvInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate($mainValue, $amount): bool
    {
        // $className = self::validatorPath.self::validatorBaseName.$billerCode;
        // $validator = new $className;

        return $this->validator->validate($mainValue, $amount);
    }
}
