<?php

namespace Web3\Validators;

use Web3\Validators\IValidator;

class BooleanValidator
{
    /**
     * validate
     *
     * @param mixed $value
     * @return bool
     */
    public static function validate($value)
    {
        return is_bool($value);
    }
}