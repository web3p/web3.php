<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\Validators;

use Web3\Validators\IValidator;

class ArrayNumberValidator
{
    /**
     * validate
     * TODO: add min & max validation
     *
     * @param array[int|float] $value
     * @return bool
     */
    public static function validate($value)
    {
        if (!is_array($value)) {
            return false;
        }
        foreach ($value as $val) {
            if (!(is_int($val) || is_float($val))) {
                return false;
            }
        }
        return true;
    }
}