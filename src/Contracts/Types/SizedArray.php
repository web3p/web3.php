<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\Contracts\Types;

use InvalidArgumentException;
use Web3\Utils;
use Web3\Contracts\Types\BaseArray;
use Web3\Formatters\IntegerFormatter;

class SizedArray extends BaseArray
{
    /**
     * construct
     * 
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * isType
     * 
     * @param string $name
     * @return bool
     */
    public function isType($name)
    {
        return (preg_match('/(\[([0-9]*)\])/', $name) === 1);
    }

    /**
     * isDynamicType
     * 
     * @return bool
     */
    public function isDynamicType()
    {
        return false;
    }

    /**
     * inputFormat
     * 
     * @param mixed $value
     * @param string $name
     * @return string
     */
    public function inputFormat($value, $name)
    {
        if (!is_array($value)) {
            throw new InvalidArgumentException('Encode value must be array');
        }
        $length = is_array($name) ? $this->staticArrayLength($name['type']) : 0;
        if ($length === 0 || count($value) > $length) {
            throw new InvalidArgumentException('Invalid value to encode sized array, expected: ' . $length . ', but got ' . count($value));
        }
        return parent::inputFormat($value, $name);
    }

    /**
     * outputFormat
     * 
     * @param mixed $value
     * @param string $name
     * @return string
     */
    public function outputFormat($value, $name)
    {
        $checkZero = str_replace('0', '', $value);

        if (empty($checkZero)) {
            return '0';
        }
        if (preg_match('/^bytes([0-9]*)/', $name, $match) === 1) {
            $size = intval($match[1]);
            $length = 2 * $size;
            $value = mb_substr($value, 0, $length);
        }
        return '0x' . $value;
    }
}