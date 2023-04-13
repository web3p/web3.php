<?php

namespace Web3\Contracts\Types;

use Web3\Contracts\Ethabi;
use Web3\Contracts\SolidityType;
use Web3\Contracts\Types\IType;

class Tuple extends SolidityType implements IType
{
	public function __construct()
	{
	}

	public function isType($name)
	{
		return (preg_match('/^tuple(\[([0-9]*)\])*$/', $name) === 1);
	}

	public function isDynamicType()
	{
		return true;
	}

	public function inputFormat($value, $name)
	{
		return $value;
	}

	public function outputFormat($value, $name, $output_type_hint)
	{
		$ethabi = Ethabi::factory();
		$types = $output_type_hint['components'] ?? false;
		if (!is_array($types)) {
			throw new InvalidArgumentException('Output type is required for tuple.');
		}
    if (is_numeric($value)) {
      $value = gmp_init($value);
    }

    return $ethabi->decodeParameters(['outputs'=>$types], $value);
	}
}