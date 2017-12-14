<?php

namespace Web3\Validators;

use Web3\Validators\IValidator;
use Web3\Validators\QuantityValidator;
use Web3\Validators\TagValidator;
use Web3\Validators\HexValidator;

class TransactionValidator
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
        if (!isset($value['from'])) {
            return false;
        }
        if (AddressValidator::validate($value['from']) === false) {
             return false;
        }
        if (isset($value['to']) && AddressValidator::validate($value['to']) === false) {
             return false;
        }
        if (isset($value['gas']) && QuantityValidator::validate($value['gas']) === false) {
             return false;
        }
        if (isset($value['gasPrice']) && QuantityValidator::validate($value['gasPrice']) === false) {
             return false;
        }
        if (isset($value['value']) && QuantityValidator::validate($value['value']) === false) {
             return false;
        }
        if (isset($value['data']) && HexValidator::validate($value['data']) === false) {
             return false;
        }
        if (isset($value['nonce']) && QuantityValidator::validate($value['nonce']) === false) {
             return false;
        }
        return true;
    }
}