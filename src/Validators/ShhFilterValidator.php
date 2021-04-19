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

class ShhFilterValidator extends Validator implements IValidator
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
            self::$verifyMessage = 'filter of shh must be array';
            return false;
        }
        if (isset($value['to']) && IdentityValidator::validate($value['to']) === false) {
            self::$verifyMessage = 'to is not valid';
            return false;
        }
        if (!isset($value['topics'])) {
            self::$verifyMessage = 'topics are required';
            return false;
        }
        if (!is_array($value['topics'])) {
            self::$verifyMessage = 'topics must be array';
            return false;
        }
        foreach ($value['topics'] as $topic) {
            if (is_array($topic)) {
                foreach ($topic as $subTopic) {
                    if (HexValidator::validate($subTopic) === false) {
                        self::$verifyMessage = 'topics are not valid';
                        return false;
                    }
                }
                continue;
            }
            if (HexValidator::validate($topic) === false) {
                if (!is_null($topic)) {
                    self::$verifyMessage = 'topics are not valid';
                    return false;
                }
            }
        }
        return true;
    }
}