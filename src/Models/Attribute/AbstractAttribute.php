<?php

namespace App\Models\Attribute;

use App\Models\AbstractModel;

abstract class AbstractAttribute extends AbstractModel
{
    protected string $name;
    protected string $type;
    protected array $items;

    public function __construct(
        string $id,
        string $name,
        string $type,
        array $items = []
    ) {
        parent::__construct($id);
        $this->name = $name;
        $this->type = $type;
        $this->items = $items;
    }

    public function getName(): string { return $this->name; }
    public function getType(): string { return $this->type; }
    public function getItems(): array { return $this->items; }

    abstract public function renderValue(string $value): string;
}