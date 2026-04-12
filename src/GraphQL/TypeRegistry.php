<?php

namespace App\GraphQL;

class TypeRegistry
{
    private static array $types = [];

    public static function get(string $type): object
    {
        if (!isset(self::$types[$type])) {
            self::$types[$type] = new $type();
        }
        return self::$types[$type];
    }
}