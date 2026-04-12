<?php

namespace App\Models\Product;

use App\Models\AbstractModel;

abstract class AbstractProduct extends AbstractModel
{
    protected string $name;
    protected bool $inStock;
    protected string $description;
    protected int $categoryId;
    protected string $brand;
    protected array $gallery;
    protected array $prices;
    protected array $attributes;

    public function __construct(
        string $id,
        string $name,
        bool $inStock,
        string $description,
        int $categoryId,
        string $brand,
        array $gallery = [],
        array $prices = [],
        array $attributes = []
    ) {
        parent::__construct($id);
        $this->name = $name;
        $this->inStock = $inStock;
        $this->description = $description;
        $this->categoryId = $categoryId;
        $this->brand = $brand;
        $this->gallery = $gallery;
        $this->prices = $prices;
        $this->attributes = $attributes;
    }

    public function getName(): string { return $this->name; }
    public function isInStock(): bool { return $this->inStock; }
    public function getDescription(): string { return $this->description; }
    public function getCategoryId(): int { return $this->categoryId; }
    public function getBrand(): string { return $this->brand; }
    public function getGallery(): array { return $this->gallery; }
    public function getPrices(): array { return $this->prices; }
    public function getAttributes(): array { return $this->attributes; }

    abstract public function getType(): string;
}