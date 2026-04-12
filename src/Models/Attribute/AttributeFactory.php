<?php

namespace App\Models\Attribute;

class AttributeFactory
{
    private static array $typeMap = [
        'swatch' => SwatchAttribute::class,
        'text' => TextAttribute::class,
    ];

    public static function create(
        string $id,
        string $name,
        string $type,
        array $items
    ): AbstractAttribute {
        $class = self::$typeMap[$type] ?? TextAttribute::class;
        return new $class($id, $name, $type, $items);
    }
}