<?php

namespace Web3\Validators;

interface IValidators
{
    /**
     * validate
     *
     * @param mixed $value
     * @return bool
     */
     public static function validate($value);
}