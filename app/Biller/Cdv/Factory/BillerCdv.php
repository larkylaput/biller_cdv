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

        $other_fields = [
            "other_fields" => [
                "due_date" => '2021-07-16'
            ]
        ];
        return $this->validator->validate($mainValue, $amount, $other_fields);
    }
}
