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

use InvalidArgumentException;
use stdClass;
use Web3\Utils;
use Web3\Formatters\Integer as IntegerFormatter;

class Ethabi
{
    /**
     * types
     * 
     * @var array
     */
    protected $types = [];

    /**
     * construct
     * 
     * @param array $types
     * @return void
     */
    public function __construct($types=[])
    {
        if (!is_array($types)) {
            $types = [];
        }
        $this->types = $types;
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
    }

    /**
     * set
     * 
     * @param string $name
     * @param mixed $value
     * @return bool
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
    public static function __callStatic($name, $arguments)
    {
        // 
    }

    /**
     * encodeFunctionSignature
     * 
     * @param string|stdClass|array $functionName
     * @return string
     */
    public function encodeFunctionSignature($functionName)
    {
        if (!is_string($functionName)) {
            $functionName = Utils::jsonMethodToString($functionName);
        }
        return mb_substr(Utils::sha3($functionName), 0, 10);
    }

    /**
     * encodeEventSignature
     * 
     * @param string|stdClass|array $functionName
     * @return string
     */
    public function encodeEventSignature($functionName)
    {
        if (!is_string($functionName)) {
            $functionName = Utils::jsonMethodToString($functionName);
        }
        return Utils::sha3($functionName);
    }

    /**
     * encodeParameter
     * 
     * @param string $type
     * @param mixed $param
     * @return string
     */
    public function encodeParameter($type, $param)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException('The type to encodeParameter must be string.');
        }
        return $this->encodeParameters([$type], [$param]);
    }

    /**
     * encodeParameters
     * 
     * @param stdClass|array $types
     * @param array $params
     * @return string
     */
    public function encodeParameters($types, $params)
    {
        // change json to array
        if ($types instanceof stdClass && isset($types->inputs)) {
            $types = Utils::jsonToArray($types, 2);
        }
        if (is_array($types) && isset($types['inputs'])) {
            $inputTypes = $types;
            $types = [];

            foreach ($inputTypes['inputs'] as $input) {
                if (isset($input['type'])) {
                    $types[] = $input['type'];
                }
            }
        }
        if (count($types) !== count($params)) {
            throw new InvalidArgumentException('encodeParameters number of types must equal to number of params.');
        }
        $typesLength = count($types);
        $solidityTypes = array_fill(0, $typesLength, 0);

        foreach ($types as $key => $type) {
            $match = [];

            if (preg_match('/^([a-zA-Z]+)/', $type, $match) === 1) {
                if (isset($this->types[$match[0]])) {
                    $className = $this->types[$match[0]];

                    if (call_user_func([$this->types[$match[0]], 'isType'], $type) === false) {
                        throw new InvalidArgumentException('Unsupport solidity parameter type: ' . $type);
                    }
                    $solidityTypes[$key] = $className;
                }
            }
        }
        $encodes = array_fill(0, $typesLength, '');

        foreach ($solidityTypes as $key => $type) {
            $encodes[$key] = call_user_func([$type, 'encode'], $params[$key], $types[$key]);
        }
        $dynamicOffset = 0;

        foreach ($solidityTypes as $key => $type) {
            $staticPartLength = $type->staticPartLength($types[$key]);
            $roundedStaticPartLength = floor(($staticPartLength + 31) / 32) * 32;

            if ($type->isDynamicType($types[$key]) || $type->isDynamicArray($types[$key])) {
                $dynamicOffset += 32;
            } else {
                $dynamicOffset += $roundedStaticPartLength;
            }
        }
        return '0x' . $this->encodeMultiWithOffset($types, $solidityTypes, $encodes, $dynamicOffset);
    }

    /**
     * encodeWithOffset
     * 
     * @param string $type
     * @param \Web3\Contracts\SolidityType $solidityType
     * @param mixed $encode
     * @param int $offset
     * @return string
     */
    protected function encodeWithOffset($type, $solidityType, $encoded, $offset)
    {
        if ($solidityType->isDynamicArray($type)) {
            $nestedName = $solidityType->nestedName($type);
            $nestedStaticPartLength = $solidityType->staticPartLength($type);
            $result = $encoded[0];

            if ($solidityType->isDynamicArray($nestedName)) {
                $previousLength = 2;

                for ($i=0; $i<count($encoded); $i++) {
                    if (isset($encoded[$i - 1])) {
                        $previousLength += abs($encoded[$i - 1][0]);
                    }
                    $result .= IntegerFormatter::format($offset + $i * $nestedStaticPartLength + $previousLength * 32);
                }
            }
            for ($i=0; $i<count($encoded); $i++) {
                // $bn = Utils::toBn($result);
                // $divided = $bn->divide(Utils::toBn(2));

                // if (is_array($divided)) {
                //     $additionalOffset = (int) $divided[0]->toString();
                // } else {
                //     $additionalOffset = 0;
                // }
                $additionalOffset = floor(mb_strlen($result) / 2);
                $result .= $this->encodeWithOffset($nestedName, $solidityType, $encoded[$i], $offset + $additionalOffset);
            }
            return mb_substr($result, 64);
        } elseif ($solidityType->isStaticArray($type)) {
            $nestedName = $solidityType->nestedName($type);
            $nestedStaticPartLength = $solidityType->staticPartLength($type);
            $result = '';

            if ($solidityType->isDynamicArray($nestedName)) {
                $previousLength = 0;

                for ($i=0; $i<count($encoded); $i++) {
                    if (isset($encoded[$i - 1])) {
                        $previousLength += abs($encoded[$i - 1])[0];
                    }
                    $result .= IntegerFormatter::format($offset + $i * $nestedStaticPartLength + $previousLength * 32);
                }
            }
            for ($i=0; $i<count($encoded); $i++) {
                // $bn = Utils::toBn($result);
                // $divided = $bn->divide(Utils::toBn(2));

                // if (is_array($divided)) {
                //     $additionalOffset = (int) $divided[0]->toString();
                // } else {
                //     $additionalOffset = 0;
                // }
                $additionalOffset = floor(mb_strlen($result) / 2);
                $result .= $this->encodeWithOffset($nestedName, $solidityType, $encoded[$i], $offset + $additionalOffset);
            }
            return $result;
        }
        return $encoded;
    }

    /**
     * encodeMultiWithOffset
     * 
     * @param array $types
     * @param array $solidityTypes
     * @param array $encodes
     * @param int $dynamicOffset
     * @return string
     */
    protected function encodeMultiWithOffset($types, $solidityTypes, $encodes, $dynamicOffset)
    {
        $result = '';

        foreach ($solidityTypes as $key => $type) {
            if ($type->isDynamicType($types[$key]) || $type->isDynamicArray($types[$key])) {
                $result .= IntegerFormatter::format($dynamicOffset);
                $e = $this->encodeWithOffset($types[$key], $type, $encodes[$key], $dynamicOffset);
                $dynamicOffset += floor(mb_strlen($e) / 2);
            } else {
                $result .= $this->encodeWithOffset($types[$key], $type, $encodes[$key], $dynamicOffset);
            }
        }
        foreach ($solidityTypes as $key => $type) {
            if ($type->isDynamicType($types[$key]) || $type->isDynamicArray($types[$key])) {
                $e = $this->encodeWithOffset($types[$key], $type, $encodes[$key], $dynamicOffset);
                // $dynamicOffset += floor(mb_strlen($e) / 2);
                $result .= $e;
            }
        }
        return $result;
    }

    /**
     * decodeParameter
     * 
     * @param string $type
     * @param mixed $param
     * @return string
     */
    public function decodeParameter($type, $param)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException('The type to decodeParameter must be string.');
        }
        return $this->decodeParameters([$type], [$param]);
    }

    /**
     * decodeParameters
     * 
     * @param stdClass|array $type
     * @param array $param
     * @return string
     */
    public function decodeParameters($type, $param)
    {}
}