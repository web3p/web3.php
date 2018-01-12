<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\Formatters;

use InvalidArgumentException;
use Web3\Utils;
use Web3\Formatters\IFormatter;

class BooleanFormatter implements IFormatter
{
    /**
     * format
     * 
     * @param mixed $value
     * @return int
     */
    public static function format($value)
    {
        if (!is_bool($value)) {
            throw new InvalidArgumentException('The value to inputFormat function must be boolean.');
        }
        return (int) $value;
    }
}