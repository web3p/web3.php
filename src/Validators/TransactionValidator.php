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

class TransactionValidator extends Validator implements IValidator
{
    /**
     * validate
     * To do: check is data optional?
     * Data is not optional on spec, see https://github.com/ethereum/wiki/wiki/JSON-RPC#eth_sendtransaction
     * 
     * @param array $value
     * @return bool
     */
    public static function validate($value)
    {
        if (!is_array($value)) {
            self::$verifyMessage = 'transaction must be array';
            return false;
        }
        if (!isset($value['from'])) {
            self::$verifyMessage = 'from is required';
            return false;
        }
        if (AddressValidator::validate($value['from']) === false) {
            self::$verifyMessage = 'from must be address';
            return false;
        }
        if (isset($value['to']) && AddressValidator::validate($value['to']) === false && $value['to'] !== '') {
            self::$verifyMessage = 'to must be address';
            return false;
        }
        if (isset($value['gas']) && QuantityValidator::validate($value['gas']) === false) {
            self::$verifyMessage = 'gas is not valid';
            return false;
        }
        if (isset($value['gasPrice']) && QuantityValidator::validate($value['gasPrice']) === false) {
            self::$verifyMessage = 'gasPrice is not valid';
            return false;
        }
        if (isset($value['value']) && QuantityValidator::validate($value['value']) === false) {
            self::$verifyMessage = 'value is not valid';
            return false;
        }
        if (isset($value['data']) && HexValidator::validate($value['data']) === false) {
            self::$verifyMessage = 'data is not valid';
            return false;
        }
        if (isset($value['nonce']) && QuantityValidator::validate($value['nonce']) === false) {
            self::$verifyMessage = 'nonce is not valid';
            return false;
        }
        return true;
    }
}