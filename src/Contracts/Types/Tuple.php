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
use Web3\Contracts\SolidityType;
use Web3\Contracts\Types\IType;
use Web3\Formatters\IntegerFormatter;

class Tuple extends SolidityType implements IType
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
        return (preg_match('/(tuple)?\((.*)\)/', $name) === 1);
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
    public function inputFormat($params, $abiTypes)
    {
        $result = [];
        $rawHead = [];
        $tail = [];
        foreach ($abiTypes as $key => $abiType) {
            if ($abiType['dynamic']) {
                $rawHead[] = null;
                $tail[] = $abiType['solidityType']->encode($params[$key], $abiType);
            } else {
                $rawHead[] = $abiType['solidityType']->encode($params[$key], $abiType);
                $tail[] = '';
            }
        }
        $headLength = 0;
        foreach ($rawHead as $head) {
            if (is_null($head)) {
                $headLength += 32;
                continue;
            }
            $headLength += (int) mb_strlen($head) / 2;
        }
        $tailOffsets = [0];
        foreach ($tail as $key => $val) {
            if ($key === count($tail) - 1) {
                break;
            }
            $tailOffsets[] = (int) (mb_strlen($val) / 2);
        }
        $headChunks = [];
        foreach ($rawHead as $key => $head) {
            if (!array_key_exists($key, $tail)) continue;
            $offset = $tailOffsets[$key];
            if (is_null($head)) {
                $headChunks[] = IntegerFormatter::format($headLength + $offset);
                continue;
            }
            $headChunks[] = $head;
        }
        return implode('', array_merge($headChunks, $tail));
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