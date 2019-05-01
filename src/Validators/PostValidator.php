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
use Web3\Validators\HexValidator;
use Web3\Validators\IdentityValidator;

class PostValidator extends Validator implements IValidator
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
            self::$verifyMessage = 'post must be array';
            return false;
        }
        if (isset($value['from']) && IdentityValidator::validate($value['from']) === false) {
            self::$verifyMessage = 'from is not valid';
            return false;
        }
        if (isset($value['to']) && IdentityValidator::validate($value['to']) === false) {
            self::$verifyMessage = 'to is not valid';
            return false;
        }
        if (!isset($value['topics']) || !is_array($value['topics'])) {
            self::$verifyMessage = 'topics are not valid';
            return false;
        }
        foreach ($value['topics'] as $topic) {
            if (IdentityValidator::validate($topic) === false) {
                self::$verifyMessage = 'topics are not valid';
                return false;
            }
        }
        if (!isset($value['payload'])) {
            self::$verifyMessage = 'payload is required';
            return false;
        }
        if (HexValidator::validate($value['payload']) === false) {
            self::$verifyMessage = 'payload is not valid';
            return false;
        }
        if (!isset($value['priority'])) {
            self::$verifyMessage = 'priority is required';
            return false;
        }
        if (QuantityValidator::validate($value['priority']) === false) {
            self::$verifyMessage = 'priority is not valid';
            return false;
        }
        if (!isset($value['ttl'])) {
            self::$verifyMessage = 'ttl is required';
            return false;
        }
        if (isset($value['ttl']) && QuantityValidator::validate($value['ttl']) === false) {
            self::$verifyMessage = 'ttl is not valid';
            return false;
        }
        return true;
    }
}