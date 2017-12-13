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

        return in_array($value, $tags);
    }
}