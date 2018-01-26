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
use Web3\Validators\QuantityValidator;
use Web3\Validators\HexValidator;
use Web3\Validators\IdentityValidator;

class PostValidator
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
            return false;
        }
        if (isset($value['from']) && IdentityValidator::validate($value['from']) === false) {
            return false;
        }
        if (isset($value['to']) && IdentityValidator::validate($value['to']) === false) {
            return false;
        }
        if (!isset($value['topics']) || !is_array($value['topics'])) {
            return false;
        }
        foreach ($value['topics'] as $topic) {
            if (IdentityValidator::validate($topic) === false) {
                return false;
            }
        }
        if (!isset($value['payload'])) {
            return false;
        }
        if (HexValidator::validate($value['payload']) === false) {
            return false;
        }
        if (!isset($value['priority'])) {
            return false;
        }
        if (QuantityValidator::validate($value['priority']) === false) {
            return false;
        }
        if (!isset($value['ttl'])) {
            return false;
        }
        if (isset($value['ttl']) && QuantityValidator::validate($value['ttl']) === false) {
            return false;
        }
        return true;
    }
}