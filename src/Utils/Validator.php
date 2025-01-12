<?php

namespace App\Utils;

class Validator
{
    public static function validate(array $fields): array
    {

        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                self::validate($value);
            } else if (empty(trim($value))) {
                throw new \Exception("Field '{$key}' is required"); 
            }
        }

        return $fields;
    }
}