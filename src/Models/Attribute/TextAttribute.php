<?php

namespace App\Models\Attribute;

class TextAttribute extends AbstractAttribute
{
    public function renderValue(string $value): string
    {
        return $value;
    }
}