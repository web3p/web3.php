<?php

/**
 * This file is part of web3.php package.
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\Validators;

use Web3\Validators\IValidator;

class QuantityValidator
{
    /**
     * validate
     *
     * @param string $value
     * @return bool
     */
    public static function validate($value)
    {
        // maybe change in_int and preg_match future
        return (is_int($value) || preg_match('/^0x[a-fA-f0-9]+$/', $value) >= 1);
    }
}