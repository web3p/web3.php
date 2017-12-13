<?php

namespace Web3\Validators;

use Web3\Validators\IValidator;

class BlockHashValidator
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
        return (preg_match('/^0x[a-fA-F0-9]{64}$/', $value) >= 1);
    }
}