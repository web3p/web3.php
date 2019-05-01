<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\Validators;

use Web3\Validators\IValidator;
use Web3\Validators\Validator;
use Web3\Validators\QuantityValidator;
use Web3\Validators\TagValidator;
use Web3\Validators\HexValidator;
use Web3\Validators\AddressValidator;

class FilterValidator extends Validator implements IValidator
{
    /**
     * validate
     *
     * @param array $value
     * @return bool
     */
    public static function validate($value)
    {
        if (!is_array($value)) {
            self::$verifyMessage = 'filter must be array';
            return false;
        }
        if (
            isset($value['fromBlock']) &&
            QuantityValidator::validate($value['fromBlock']) === false &&
            TagValidator::validate($value['fromBlock']) === false
            ) {
            self::$verifyMessage = 'fromBlock is not valid';
            return false;
        }
        if (
            isset($value['toBlock']) &&
            QuantityValidator::validate($value['toBlock']) === false &&
            TagValidator::validate($value['toBlock']) === false
            ) {
            self::$verifyMessage = 'toBlock is not valid';
            return false;
        }
        if (isset($value['address'])) {
            if (is_array($value['address'])) {
                foreach ($value['address'] as $address) {
                    if (AddressValidator::validate($address) === false) {
                        self::$verifyMessage = 'address is not valid';
                        return false;
                    }
                }
            } elseif (AddressValidator::validate($value['address']) === false) {
                self::$verifyMessage = 'address is not valid';
                return false;
            }
        }
        if (isset($value['topics']) && is_array($value['topics'])) {
            foreach ($value['topics'] as $topic) {
                if (is_array($topic)) {
                    foreach ($topic as $v) {
                        if (isset($v) && HexValidator::validate($v) === false) {
                            self::$verifyMessage = 'topics are not valid';
                            return false;
                        }
                    }
                } else {
                    if (isset($topic) && HexValidator::validate($topic) === false) {
                        self::$verifyMessage = 'topics are not valid';
                        return false;
                    }
                }
            }
        }
        return true;
    }
}