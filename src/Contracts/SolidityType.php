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

class SolidityType
{
    /**
     * construct
     * 
     * @return void
     */
    // public function  __construct() {}

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
			//解决 encode 
	
            if ($this->isDynamicType($nestedName)){
                $start = 0;
                foreach ($value as $k => $val) {
                    if ($start == 0){
                        $l = $length * 32;
                    }else{
                        $v_1 = Utils::toHex($value[$k-1]);
                        $l = (floor((mb_strlen($v_1) + 63) / 64)+1) * 32;
                    }
                    $start += $l;
                    $result[] = IntegerFormatter::format($start);
                }
            }

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
            $arrayOffset = (int) Utils::toBn('0x' . mb_substr($value, $offset * 2, 64))->toString();	//32
            $length = (int) Utils::toBn('0x' . mb_substr($value, $arrayOffset * 2, 64))->toString();  //数组的个数
            $arrayStart = $arrayOffset + 32;
			
            $nestedName = $this->nestedName($name);
			
			if($nestedName=='bytes' || $nestedName=='string')
			{
				$mA = $arrayStart*2;
				$mAA = ($mA + (64*1));
				for($i=0;$i<$length;$i++)
				{
					$mAA = ($mA + (64*$i)); //目前的定位
					
					$mB	= (int) Utils::toBn('0x' . mb_substr($value, ($mA + (64*$i)), 64))->toString();
					
					
					$mBB= $mA+($mB*2);
					#clear $mBB.PHP_EOL;
					$mC = (int) Utils::toBn('0x' . mb_substr($value, $mBB, 64))->toString();
					$mCC = (floor($mC/32)+1)*64;
					$mD  = mb_substr($value, ($mBB+64), $mCC);
					#echo  $mD.PHP_EOL;
					#echo mb_substr($value, $mBB, 64).PHP_EOL;
					
					#echo "mb ={$mB} mBB={$mBB} mc={$mC} mcc={$mCC}".PHP_EOL;
					$result[] = $this->decode($value, $mBB , $nestedName);
				}
				
			}else 
			{
				$nestedStaticPartLength = $this->staticPartLength($nestedName);
				$roundedNestedStaticPartLength = floor(($nestedStaticPartLength + 31) / 32) * 32;
				$result = [];

				for ($i=0; $i<$length * $roundedNestedStaticPartLength; $i+=$roundedNestedStaticPartLength) {
					$result[] = $this->decode($value, $arrayStart + $i, $nestedName);
				}				
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
			
			if($name=='bytes' || $name=='string')
			{
				$mC 	= (int) Utils::toBn('0x' . mb_substr($value, $offset, 64))->toString();
				$mCC 	= (floor($mC/32)+1)*64;
				$param  	= mb_substr($value, ($offset), ($mCC+64));	
			}else
			{
				$dynamicOffset = (int) Utils::toBn('0x' . mb_substr($value, $offset * 2, 64))->toString();
				$length = (int) Utils::toBn('0x' . mb_substr($value, $dynamicOffset * 2, 64))->toString();
				$roundedLength = floor(($length + 31) / 32);
				$param = mb_substr($value, $dynamicOffset * 2, ( 1 + $roundedLength) * 64);				
			}
            return $this->outputFormat($param, $name);
        }
        $length = $this->staticPartLength($name);
        $param = mb_substr($value, $offset * 2, $length * 2);

        return $this->outputFormat($param, $name);
    }
}
