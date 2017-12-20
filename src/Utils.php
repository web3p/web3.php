<?php

namespace Web3;

use InvalidArgumentException;

class Utils
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
     * toHex
     * 
     * @param string $value
     * @param bool $isPrefix
     * @return string
     */
    public static function toHex($value, $isPrefix=false)
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to toHex function must be string.');
        }
        if ($isPrefix) {
            return '0x' . implode('', unpack('H*', $value));
        }
        return implode('', unpack('H*', $value));
    }

    /**
     * hexToBin
     * 
     * @param string
     * @return string
     */
    public static function hexToBin($value)
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to toHex function must be string.');
        }
        $hexString = str_replace('0x', '', $value);

        return pack('H*', $hexString);
    }
}