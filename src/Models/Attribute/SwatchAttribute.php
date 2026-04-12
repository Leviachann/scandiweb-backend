<?php

namespace App\Models\Attribute;

class SwatchAttribute extends AbstractAttribute
{
    public function renderValue(string $value): string
    {
        return '<span style="background-color:' . $value . '"></span>';
    }
}