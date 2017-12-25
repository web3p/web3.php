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
use phpseclib\Math\BigInteger as BigNumber;

class Utils
{
    /**
     * SHA3_NULL_HASH
     * 
     * @const string
     */
    const SHA3_NULL_HASH = 'c5d2460186f7233c927e7db2dcc703c0e500b653ca82273b7bfad8045d85a470';

    /**
     * UNITS
     * from web3.js
     * 
     * @const array
     */
    const UNITS = [
        'noether' => '0',
        'wei' => '1',
        'kwei' => '1000',
        'Kwei' => '1000',
        'babbage' => '1000',
        'femtoether' => '1000',
        'mwei' => '1000000',
        'Mwei' => '1000000',
        'lovelace' => '1000000',
        'picoether' => '1000000',
        'gwei' => '1000000000',
        'Gwei' => '1000000000',
        'shannon' => '1000000000',
        'nanoether' => '1000000000',
        'nano' => '1000000000',
        'szabo' => '1000000000000',
        'microether' => '1000000000000',
        'micro' => '1000000000000',
        'finney' => '1000000000000000',
        'milliether' => '1000000000000000',
        'milli' => '1000000000000000',
        'ether' => '1000000000000000000',
        'kether' => '1000000000000000000000',
        'grand' => '1000000000000000000000',
        'mether' => '1000000000000000000000000',
        'gether' => '1000000000000000000000000000',
        'tether' => '1000000000000000000000000000000'
    ];

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
        if (strpos($value, '0x') === 0) {
            $count = 1;
            $value = str_replace('0x', '', $value, $count);
        }
        return pack('H*', $value);
    }

    /**
     * isZeroPrefixed
     * 
     * @param string
     * @return bool
     */
    public static function isZeroPrefixed($value)
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to zeroPrefixed function must be string.');
        }
        return (strpos($value, '0x') === 0);
    }

    /**
     * stripZero
     * 
     * @param string $value
     * @return string
     */
    public static function stripZero($value)
    {
        if (self::isZeroPrefixed($value)) {
            $count = 1;
            return str_replace('0x', '', $value, $count);
        }
        return $value;
    }

    /**
     * sha3
     * keccak256
     * 
     * @param string $value
     * @return string
     */
    public static function sha3($value)
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to sha3 function must be string.');
        }
        if (strpos($value, '0x') === 0) {
            $value = self::hexToBin($value);
        }
        $hash = Keccak::hash($value, 256);

        if ($hash === self::SHA3_NULL_HASH) {
            return null;
        }
        return '0x' . $hash;
    }

    /**
     * toWei
     * 
     * @param BigNumber|string|int $number
     * @param string $unit
     * @return \phpseclib\Math\BigInteger
     */
    public static function toWei($number, $unit)
    {
        if (is_int($number)) {
            $bn = new BigNumber($number);
        } elseif (is_string($number)) {
            if (self::isZeroPrefixed($number)) {
                $number = self::stripZero($number);
                $bn = new BigNumber($number, 16);
            } else {
                $bn = new BigNumber($number);
            }
        } elseif (!$number instanceof BigNumber){
            throw new InvalidArgumentException('toWei number must be BigNumber, string or int.');
        }
        if (!is_string($unit)) {
            throw new InvalidArgumentException('toWei unit must be string.');
        }
        if (!isset(self::UNITS[$unit])) {
            throw new InvalidArgumentException('toWei doesn\'t support ' . $unit . ' unit.');
        }
        $bnt = new BigNumber(self::UNITS[$unit]);

        return $bn->multiply($bnt);
    }

    /**
     * toEther
     * 
     * @param BigNumber|string|int $number
     * @param string $unit
     * @return array
     */
    public static function toEther($number, $unit)
    {
        if ($unit === 'ether') {
            throw new InvalidArgumentException('Please use another unit.');
        }
        $wei = self::toWei($number, $unit);
        $bnt = new BigNumber(self::UNITS['ether']);

        return $wei->divide($bnt);
    }
}