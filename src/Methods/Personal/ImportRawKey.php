<?php

/**
 * This file is part of web3.php package.
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\Methods\Personal;

use InvalidArgumentException;
use Web3\Methods\EthMethod;
use Web3\Validators\PrivateValidator;
use Web3\Validators\StringValidator;
use Web3\Formatters\StringFormatter;
use Web3\Formatters\PrivateFormatter;

class ImportRawKey extends EthMethod
{
    /**
     * validators
     *
     * @var array
     */
    protected $validators = [
        PrivateValidator::class, StringValidator::class
    ];

    /**
     * inputFormatters
     *
     * @var array
     */
    protected $inputFormatters = [
        PrivateFormatter::class, StringFormatter::class
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
    protected $defaultValues = [];

    /**
     * construct
     *
     * @param string $method
     * @param array $arguments
     * @return void
     */
    // public function __construct($method='', $arguments=[])
    // {
    //     parent::__construct($method, $arguments);
    // }
}
