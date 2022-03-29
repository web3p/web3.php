<?php
/**
 * This file is part of web3.php package.
 * @author Halil Beycan <halilbeycan0@gmail.com>
 * @license MIT
 */

namespace Web3;

use Web3\Contracts\Ethabi;
use Web3\Contracts\Types\Address;
use Web3\Contracts\Types\Boolean;
use Web3\Contracts\Types\Bytes;
use Web3\Contracts\Types\DynamicBytes;
use Web3\Contracts\Types\Integer;
use Web3\Contracts\Types\Str;
use Web3\Contracts\Types\Uinteger;
use phpseclib\Math\BigInteger as BN;

class AbiDecoder
{
    /**
     * @var array
     */
    private $abi = [];

    /**
     * @var Ethabi
     */
    private $ethAbi;

    /**
     * @var array
     */
    private $methodIds = [];

    /**
     * @param array|null $abi
     */
    public function __construct(?array $abi = null)
    {
        $this->abi = is_null($abi) ? json_decode(file_get_contents(dirname(__DIR__) . '/resources/abi.json')) : $abi;

        $this->ethAbi = new Ethabi([
            'address' => new Address,
            'bool' => new Boolean,
            'bytes' => new Bytes,
            'dynamicBytes' => new DynamicBytes,
            'int' => new Integer,
            'string' => new Str,
            'uint' => new Uinteger,
        ]);
        
        array_map([$this, 'parseMethodIds'], $this->abi);
    }

    /**
     * @param object $input
     * @return string
     */
    private function typeToString(object $input) : string
    {
        if ($input->type === "tuple") {
            return "(" . implode(',', array_map([$this, 'typeToString'], $input->components)) . ")";
        }

        return $input->type;
    }

    /**
     * @param array $inputs
     * @return string
     */
    private function parseInputs(array $inputs) : string
    {
        return "(" . implode(',', array_map([$this, 'typeToString'], $inputs)) . ")";
    }

    /**
     * @param object $obj
     * @return void
     */
    private function parseMethodIds(object $obj)
    {
        if (isset($obj->name)) {
            $signature = Utils::sha3($obj->name . $this->parseInputs($obj->inputs));

            if ($obj->type === "event") {
                $this->methodIds[substr($signature, 2)] = $obj;
            } else {
                $this->methodIds[substr($signature, 2, 8)] = $obj;
            }
        }
    }

    /**
     * @param string $input
     * @return object
     */
    public function decodeInput(string $input) : object
    {
        $abiItem = $this->methodIds[substr($input, 2, 8)];
        $types = array_column($abiItem->inputs, 'type');
        
        if ($abiItem) {
            $decoded = $this->ethAbi->decodeParameters($types, substr($input, 10));

            $retData = (object) [
                "name" => $abiItem->name,
                "params" => [],
            ];
            
            for ($i = 0; $i < count($decoded); $i++) {
                $param = $decoded[$i];
                $parsedParam = $param;
                $isArray = is_array($param);
                $isUint = strpos($abiItem->inputs[$i]->type, "uint") !== false;
                $isInt = strpos($abiItem->inputs[$i]->type, "int") !== false;
                $isAddress = strpos($abiItem->inputs[$i]->type, "address") !== false;

                if ($isUint || $isInt) {
                    if ($isArray) {
                        $parsedParam = array_map(function($val) {
                            return (new BN($val))->toString();
                        }, $param);
                    } else {
                        $parsedParam = (new BN($param))->toString();
                    }
                }
    
                if ($isAddress) {
                    if ($isArray) {
                        $parsedParam = array_map('strtolower', $param);
                } else {
                        $parsedParam = strtolower($param);
                    }
                }

                $retData->params[] = [
                    "name" => $abiItem->inputs[$i]->name,
                    "value" => $parsedParam,
                    "type" => $abiItem->inputs[$i]->type,
                ];
            }
        }

        return $retData;
    }

    /**
     * @param array $logs
     * @return array
     */
    public function decodeLogs(array $logs) : array
    {
        $logs = array_filter($logs, function($log) {
            return count($log->topics) > 0;
        });

        return array_map(function($log) {
            
            $method = $this->methodIds[substr($log->topics[0], 2)];

            if ($method) {
                
                $logData = $log->data;
                $decodedParams = [];
                $dataIndex = 0;
                $topicsIndex = 1;
                $dataTypes = [];

                array_map(function($input) use (&$dataTypes) {
                    if (!$input->indexed) {
                        $dataTypes[] = $input->type;
                    }
                }, $method->inputs);

                $decoded = $this->ethAbi->decodeParameters($dataTypes, substr($logData, 2));

                array_map(function($input) use ($log, &$decodedParams, &$topicsIndex, &$dataIndex, $decoded) {
                    
                    $decodedP = (object) [
                        "name" => $input->name,
                        "type" => $input->type,
                    ];

                    if ($input->indexed) {
                        $decodedP->value = $log->topics[$topicsIndex];
                        $topicsIndex++;
                    } else {
                        $decodedP->value = $decoded[$dataIndex];
                        $dataIndex++;
                    }

                    if ($input->type === "address") {
                        $decodedP->value = strtolower($decodedP->value);
                        if (strlen($decodedP->value) > 42) {
                            $toRemove = strlen($decodedP->value) - 42;
                            $decodedP->value = "0x" . substr($decodedP->value, -40);
                        }
                    }

                    if (
                        $input->type === "uint256" ||
                        $input->type === "uint8" ||
                        $input->type === "int"
                    ) {
                        if (is_string($decodedP->value) && str_starts_with($decodedP->value, '0x')) {
                            $decodedP->value = (new BN(substr($decodedP->value, 2), 16))->toString(10);
                        } else {
                            $decodedP->value = (new BN($decodedP->value))->toString(10);
                        }              
                    }
    
                    $decodedParams[] = $decodedP;

                }, $method->inputs);

                
                return (object) [
                    "name" => $method->name,
                    "events" => $decodedParams,
                    "address" => $log->address,
                ];
            }

        }, $logs);
    }
}