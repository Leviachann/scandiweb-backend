<?php

namespace App\Models;

abstract class AbstractModel
{
    protected int|string $id;

    public function __construct(int|string $id)
    {
        $this->id = $id;
    }

    public function getId(): int|string
    {
        return $this->id;
    }
}