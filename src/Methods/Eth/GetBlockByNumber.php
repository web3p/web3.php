<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\Methods\Eth;

use InvalidArgumentException;
use Web3\Methods\EthMethod;
use Web3\Validators\QuantityValidator;
use Web3\Validators\TagValidator;
use Web3\Validators\BooleanValidator;
use Web3\Formatters\OptionalQuantityFormatter;
use Web3\Formatters\BooleanFormatter;

class GetBlockByNumber extends EthMethod
{
    /**
     * validators
     * 
     * @var array
     */
    protected $validators = [
        [
            QuantityValidator::class, TagValidator::class
        ], BooleanValidator::class
    ];

    /**
     * inputFormatters
     * 
     * @var array
     */
    protected $inputFormatters = [
        OptionalQuantityFormatter::class, BooleanFormatter::class
    ];

    /**
     * outputFormatters
     * 
     * @var array
     */
    protected $outputFormatters = [];

    /**
     * defaultValues
     * 
     * @var array
     */
    protected $defaultValues = [
        0 => 'latest'
    ];
}