<?php

namespace Web3\Validators;

use Web3\Validators\IValidator;

class NonceValidator
{
    /**
     * validate
     *
     * @param string $value
     * @return bool
     */
    public static function validate($value)
    {
        if (!is_string($value)) {
            return false;
        }
        return (preg_match('/^0x[a-fA-F0-9]{16}$/', $value) >= 1);
    }
}