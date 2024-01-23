<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) abaowu <abaowu@gmail.com>
 * 
 * @author abaowu <abaowu@gmail.com>
 * @license MIT
 */

namespace Web3\Contracts\Types;

use Web3\Contracts\SolidityType;
use Web3\Contracts\Types\IType;

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
        return (preg_match('/^tuple(\[([0-9]*)\])*$/', $name) === 1);
    }

    /**
     * isDynamicType
     * 
     * @return bool
     */
    public function isDynamicType()
    {
        return true;
    }

    /**
     * inputFormat
     * to do: iban
     * 
     * @param mixed $value
     * @param string $name
     * @return string
     */
    public function inputFormat($value, $name)
    {
        // $value = (string) $value;

        // if (Utils::isAddress($value)) {
        //     $value = mb_strtolower($value);

        //     if (Utils::isZeroPrefixed($value)) {
        //         $value = Utils::stripZero($value);
        //     }
        // }
        // $value = IntegerFormatter::format($value);

        return $value;
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
        // return '0x' . mb_substr($value, 24, 40);
        return $value;
    }
}