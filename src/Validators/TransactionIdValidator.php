<?php

/**
 * @author Halil Beycan <halilbeycan0@gmail.com>
 * @license MIT
 */

namespace Web3\Validators;

class TransactionIdValidator
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
        
        return (preg_match('/^0x[a-fA-F0-9]{66}$/', $value) >= 1);
    }
}