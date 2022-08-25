<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\Contracts;

use Web3\Utils;
use Web3\Formatters\IntegerFormatter;
use InvalidArgumentException;
use Web3\Contracts\Types\Address;
use Web3\Contracts\Types\Boolean;
use Web3\Contracts\Types\Bytes;
use Web3\Contracts\Types\DynamicBytes;
use Web3\Contracts\Types\Integer;
use Web3\Contracts\Types\Str;
use Web3\Contracts\Types\Uinteger;
use Web3\Contracts\Types\Tuple;


class SolidityType
{
    private array $types = [];

    /**
     * construct
     * 
     * @return void
     */
    // public function  __construct() {}

    /**
     * set types
     */
    private function  setTypes() 
    {
        if(!empty($this->types)){
            return $this->types;
        }
        $this->types = [
            'address' => new Address,
            'bool' => new Boolean,
            'bytes' => new Bytes,
            'dynamicBytes' => new DynamicBytes,
            'int' => new Integer,
            'string' => new Str,
            'uint' => new Uinteger,
            'tuple' => new Tuple,
        ];
        return $this->types;
    }

    /**
     * get
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], []);
        }
        return false;
    }

    /**
     * set
     * 
     * @param string $name
     * @param mixed $value
     * @return mixed;
     */
    public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);

        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], [$value]);
        }
        return false;
    }

    /**
     * callStatic
     * 
     * @param string $name
     * @param array $arguments
     * @return void
     */
    // public static function __callStatic($name, $arguments) {}

    /**
     * nestedTypes
     * 
     * @param string $name
     * @return mixed
     */
    public function nestedTypes($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('nestedTypes name must string.');
        }
        $matches = [];

        if (preg_match_all('/(\[[0-9]*\])/', $name, $matches, PREG_PATTERN_ORDER) >= 1) {
            return $matches[0];
        }
        return false;
    }

    /**
     * nestedName
     * 
     * @param string $name
     * @return string
     */
    public function nestedName($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('nestedName name must string.');
        }
        $nestedTypes = $this->nestedTypes($name);

        if ($nestedTypes === false) {
            return $name;
        }
        return mb_substr($name, 0, mb_strlen($name) - mb_strlen($nestedTypes[count($nestedTypes) - 1]));
    }

    /**
     * isDynamicArray
     * 
     * @param string $name
     * @return bool
     */
    public function isDynamicArray($name)
    {
        $nestedTypes = $this->nestedTypes($name);

        return $nestedTypes && preg_match('/[0-9]{1,}/', $nestedTypes[count($nestedTypes) - 1]) !== 1;
    }

    /**
     * isStaticArray
     * 
     * @param string $name
     * @return bool
     */
    public function isStaticArray($name)
    {
        $nestedTypes = $this->nestedTypes($name);

        return $nestedTypes && preg_match('/[0-9]{1,}/', $nestedTypes[count($nestedTypes) - 1]) === 1;
    }

    /**
     * staticArrayLength
     * 
     * @param string $name
     * @return int
     */
    public function staticArrayLength($name)
    {
        $nestedTypes = $this->nestedTypes($name);

        if ($nestedTypes === false) {
            return 1;
        }
        $match = [];

        if (preg_match('/[0-9]{1,}/', $nestedTypes[count($nestedTypes) - 1], $match) === 1) {
            return (int) $match[0];
        }
        return 1;
    }

    /**
     * staticPartLength
     * 
     * @param string $name
     * @return int
     */
    public function staticPartLength($name)
    {
        $nestedTypes = $this->nestedTypes($name);

        if ($nestedTypes === false) {
            $nestedTypes = ['[1]'];
        }
        $count = 32;

        foreach ($nestedTypes as $type) {
            $num = mb_substr($type, 1, 1);

            if (!is_numeric($num)) {
                $num = 1;
            } else {
                $num = intval($num);
            }
            $count *= $num;
        }

        return $count;
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
     * encode
     * 
     * @param mixed $value
     * @param string $name
     * @return string
     */
    public function encode($value, $name)
    {
        if ($this->isDynamicArray($name)) {
            $length = count($value);
            $nestedName = $this->nestedName($name);
            $result = [];
            $result[] = IntegerFormatter::format($length);

            foreach ($value as $val) {
                $result[] = $this->encode($val, $nestedName);
            }
            return $result;
        } elseif ($this->isStaticArray($name)) {
            $length = $this->staticArrayLength($name);
            $nestedName = $this->nestedName($name);
            $result = [];

            foreach ($value as $val) {
                $result[] = $this->encode($val, $nestedName);
            }
            return $result;
        }
        return $this->inputFormat($value, $name);
    }

    /**
     * decode
     * 
     * @param mixed $value
     * @param string $offset
     * @param string $name
     * @return array
     */
    public function decode($value, $offset, $name)
    {
        if ($this->isDynamicArray($name)) {
            $arrayOffset = (int) Utils::toBn('0x' . mb_substr($value, $offset * 2, 64))->toString();
            $length = (int) Utils::toBn('0x' . mb_substr($value, $arrayOffset * 2, 64))->toString();
            $arrayStart = $arrayOffset + 32;

            $nestedName = $this->nestedName($name);
            $nestedStaticPartLength = $this->staticPartLength($nestedName);
            $roundedNestedStaticPartLength = floor(($nestedStaticPartLength + 31) / 32) * 32;
            $result = [];

            for ($i=0; $i<$length * $roundedNestedStaticPartLength; $i+=$roundedNestedStaticPartLength) {
                $result[] = $this->decode($value, $arrayStart + $i, $nestedName);
            }
            return $result;
        } elseif ($this->isStaticArray($name)) {
            $length = $this->staticArrayLength($name);
            $arrayStart = $offset;

            $nestedName = $this->nestedName($name);
            $nestedStaticPartLength = $this->staticPartLength($nestedName);
            $roundedNestedStaticPartLength = floor(($nestedStaticPartLength + 31) / 32) * 32;
            $result = [];

            for ($i=0; $i<$length * $roundedNestedStaticPartLength; $i+=$roundedNestedStaticPartLength) {
                $result[] = $this->decode($value, $arrayStart + $i, $nestedName);
            }
            return $result;
        } elseif ($this->isDynamicType()) {
            $dynamicOffset = (int) Utils::toBn('0x' . mb_substr($value, $offset * 2, 64))->toString();
            $length = (int) Utils::toBn('0x' . mb_substr($value, $dynamicOffset * 2, 64))->toString();
            $roundedLength = floor(($length + 31) / 32);
            $param = mb_substr($value, $dynamicOffset * 2, ( 1 + $roundedLength) * 64);
            return $this->outputFormat($param, $name);
        }
        $length = $this->staticPartLength($name);
        $param = mb_substr($value, $offset * 2, $length * 2);

        return $this->outputFormat($param, $name);
    }

    /**
     * decode struct data
     * 
     * @author abaowu <abaowu@gmail.com>
     * @param array $type tuple struct
     * @param mix $value tuple value
     * @return array
     */
    public function decodeTuple($type, $value) 
    {
        $outputTypes = $type["components"];
        $typesLength = count($outputTypes);
        $solidityTypes = $this->getSolidityTypes($outputTypes);
        $offsets = array_fill(0, $typesLength, 0);
        
        for ($i=0; $i<$typesLength; $i++) {
            $offsets[$i] = $solidityTypes[$i]->staticPartLength($outputTypes[$i]['type']);
        }
        for ($i=1; $i<$typesLength; $i++) {
            $offsets[$i] += $offsets[$i - 1];
        }
        for ($i=0; $i<$typesLength; $i++) {
            $offsets[$i] -= $solidityTypes[$i]->staticPartLength($outputTypes[$i]['type']);
        }
        $result = [];
        $param = mb_strtolower(Utils::stripZero($value));
        
        for ($i=0; $i<$typesLength; $i++) {
            if (isset($outputTypes[$i]['name']) && empty($outputTypes[$i]['name']) === false) {
                $result[$outputTypes[$i]['name']] = $solidityTypes[$i]->decode($param, $offsets[$i], $outputTypes[$i]['type']);
            } else {
                $result[$i] = $solidityTypes[$i]->decode($param, $offsets[$i], $outputTypes[$i]['type']);
            }
        }

        return $result;
    
    }


    /**
     * getSolidityTypes
     * 
     * @param array $types
     * @return array
     */
    protected function getSolidityTypes($types)
    {
        if (!is_array($types)) {
            throw new InvalidArgumentException('Types must be array');
        }
        $solidityTypes = array_fill(0, count($types), 0);
        $this->setTypes();

        foreach ($types as $key => $type) {
            $match = [];
            $type = $type['type'];
            if (preg_match('/^([a-zA-Z]+)/', $type, $match) === 1) {
                if (isset($this->types[$match[0]])) {
                    $className = $this->types[$match[0]];

                    if (call_user_func([$this->types[$match[0]], 'isType'], $type) === false) {
                        // check dynamic bytes
                        if ($match[0] === 'bytes') {
                            $className = $this->types['dynamicBytes'];
                        } else {
                            throw new InvalidArgumentException('Unsupport solidity parameter type: ' . $type);
                        }
                    }
                    $solidityTypes[$key] = $className;
                }
            }
        }
        return $solidityTypes;
    }

}