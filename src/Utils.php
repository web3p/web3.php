<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3;

use InvalidArgumentException;
use kornrunner\Keccak;

class Utils
{
    /**
     * SHA3_NULL_HASH
     * 
     * @const string
     */
    const SHA3_NULL_HASH = 'c5d2460186f7233c927e7db2dcc703c0e500b653ca82273b7bfad8045d85a470';

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

    /**
     * sha3
     * keccak256
     * 
     * @param string $value
     * @return string
     */
    public function sha3($value)
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to sha3 function must be string.');
        }
        if (preg_match('/^0x/', $value) > 0) {
            $value = self::hexToBin($value);
        }
        $hash = Keccak::hash($value, 256);

        if ($hash === self::SHA3_NULL_HASH) {
            return null;
        }
        return '0x' . $hash;
    }
}