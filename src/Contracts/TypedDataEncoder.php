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
use Web3\Formatters\IntegerFormatter;
use Web3\Contracts\Ethabi;
use Web3\Contracts\Types\Address;
use Web3\Contracts\Types\Boolean;
use Web3\Contracts\Types\Bytes;
use Web3\Contracts\Types\DynamicBytes;
use Web3\Contracts\Types\Integer;
use Web3\Contracts\Types\Str;
use Web3\Contracts\Types\Uinteger;

class TypedDataEncoder
{
    /**
     * ethabi
     * 
     * @var \Web3\Contracts\Ethabi
     */
    protected $ethabi;

    /**
     * 
     */
    protected $eip712SolidityTypes = [
        'bool', 'address', 'string', 'bytes', 'uint', 'int',
        'int8', 'int16', 'int24', 'int32', 'int40', 'int48', 'int56', 'int64', 'int72', 'int80', 'int88', 'int96', 'int104', 'int112', 'int120', 'int128', 'int136', 'int144', 'int152', 'int160', 'int168', 'int176', 'int184', 'int192', 'int200', 'int208', 'int216', 'int224', 'int232', 'int240', 'int248', 'int256',
        'uint8', 'uint16', 'uint24', 'uint32', 'uint40', 'uint48', 'uint56', 'uint64', 'uint72', 'uint80', 'uint88', 'uint96', 'uint104', 'uint112', 'uint120', 'uint128', 'uint136', 'uint144', 'uint152', 'uint160', 'uint168', 'uint176', 'uint184', 'uint192', 'uint200', 'uint208', 'uint216', 'uint224', 'uint232', 'uint240', 'uint248', 'uint256',
        'bytes1', 'bytes2', 'bytes3', 'bytes4', 'bytes5', 'bytes6', 'bytes7', 'bytes8', 'bytes9', 'bytes10', 'bytes11', 'bytes12', 'bytes13', 'bytes14', 'bytes15', 'bytes16', 'bytes17', 'bytes18', 'bytes19', 'bytes20', 'bytes21', 'bytes22', 'bytes23', 'bytes24', 'bytes25', 'bytes26', 'bytes27', 'bytes28', 'bytes29', 'bytes30', 'bytes31', 'bytes32'
    ];

    /**
     * construct
     * 
     * @return void
     */
    public function __construct()
    {
        $this->ethabi = new Ethabi([
            'address' => new Address,
            'bool' => new Boolean,
            'bytes' => new Bytes,
            'dynamicBytes' => new DynamicBytes,
            'int' => new Integer,
            'string' => new Str,
            'uint' => new Uinteger,
        ]);
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
     * @return mixed
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
    {}

    /**
     * encode
     * 
     * @return bool
     */
    function encode()
    {
        return '';
    }

    /**
     * encodeField
     * 
     * @param array $types
     * @param string $name
     * @param string $type
     * @param mixed $value
     * @return bool
     */
    function encodeField(array $types, string $name, string $type, mixed $value)
    {
        return '';
    }

    /**
     * strEndsWith
     * 
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function strEndsWith(string $haystack, string $needle)
    {
        $needle_len = strlen($needle);
        return ($needle_len === 0 || 0 === substr_compare($haystack, $needle, - $needle_len));
    }

    /**
     * findType
     * 
     * @param string $type
     * @param array $types
     * @return string
     */
    protected function findType(string $type, array $types)
    {
        $result = [];
        if ($this->strEndsWith($type, ']')) {
            $pos = strpos($type, '[');
            $type = ($pos !== false) ? substr($type, 0, $pos) : $typs;
        }
        if (in_array($type, $this->eip712SolidityTypes) || in_array($type, $result)) {
            return $result;
        } else if (!array_key_exists($type, $types)) {
            throw new InvalidArgumentException('No definition of type ' . $type);
        }

        $result[] = $type;
        foreach ($types[$type] as $field) {
            $subResult = $this->findType($field['type'], $types);
            if (count($subResult) > 0) {
                $result = array_merge($result, $subResult);
            }
        }
        return $result;
    }

    /**
     * encodeType
     * 
     * @param string $type
     * @param array $types
     * @return string
     */
    protected function encodeType(string $type, array $types)
    {
        $result = '';
        $unsortedDeps = $this->findType($type, $types);
        if (in_array($type, $unsortedDeps)) {
            $unsortedDeps = array_splice($unsortedDeps, array_search($type, $unsortedDeps), 1);
        } else {
            sort($unsortedDeps);
        }
        $deps = [ $type ];
        $deps = array_merge($unsortedDeps);
        foreach ($deps as $type) {
            $params = [];
            foreach ($types[$type] as $param) {
                $params[] = $param['type'] . ' ' . $param['name'];
            }
            $result .= $type . '(' . implode(',', $params) . ')';
        }
        return $result;
    }

    /**
     * hashType
     * 
     * @param string $type
     * @param array $types
     * @return string
     */
    protected function hashType(string $type, array $types)
    {
        $encodedType = $this->encodeType($type, $types);
        return Utils::sha3($encodedType);
    }

    /**
     * encodeData
     * 
     * @param string $type
     * @param array $types
     * @param array $data
     * @return string
     */
    protected function encodeData(string $type, array $types, array $data)
    {
        $encodedTypes = ['bytes32'];
        $encodedValues = [$this->hashType($type, $types)];
        var_dump($this->hashType($type, $types));
        return 'zz';
    }

    /**
     * hashStruct
     * 
     * @param string $type
     * @param array $types
     * @param array $data
     * @return string
     */
    protected function hashStruct(string $type, array $types, array $data)
    {
        $encodedData = $this->encodeData($type, $types, $data);
        return Utils::sha3($encodedData);
    }

    /**
     * hashEIP712Message
     * 
     * @param array $types
     * @param array $message
     * @return 
     */
    public function hashEIP712Message(array $types, array $message)
    {}

    /**
     * hashDomain
     * 
     * @param array $domain
     * @return string
     */
    public function hashDomain(array $domainData)
    {
        $eip721Domain = [
            'name' => [
                'name' => 'name',
                'type' => 'string'
            ],
            'version' => [
                'name' => 'version',
                'type' => 'string'
            ],
            'chainId' => [
                'name' => 'chainId',
                'type' => 'uint256'
            ],
            'verifyingContract' => [
                'name' => 'verifyingContract',
                'type' => 'address'
            ],
            'salt' => [
                'name' => 'salt',
                'type' => 'bytes32'
            ]
        ];
        $domainTypes = [
            'EIP712Domain' => []
        ];
        foreach ($domainData as $key => $data) {
            if (!array_key_exists($key, $eip721Domain)) {
                throw new InvalidArgumentException('Invalid domain key: ' + $key);
            } else {
                $domainTypes['EIP712Domain'][] = $eip721Domain[$key];
            }
        }
        return $this->hashStruct('EIP712Domain', $domainTypes, $domainData);
    }
}