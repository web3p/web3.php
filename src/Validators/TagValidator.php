<?php

namespace Web3\Validators;

use Web3\Validators\IValidator;

class TagValidator
{
    /**
     * validate
     *
     * @param string $value
     * @return bool
     */
    public static function validate($value)
    {
        $tags = [
            'latest', 'earliest', 'pending'
        ];

        // maybe change in_int future
        return (is_int($value) || in_array($value, $tags));
    }
}