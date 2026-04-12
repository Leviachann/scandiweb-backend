<?php

namespace App\GraphQL\Resolvers;

use App\Database\Connection;
use App\Models\Attribute\AttributeFactory;
use App\Models\Attribute\AbstractAttribute;

class AttributeResolver
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance()->getPdo();
    }

    public function getByProductId(string $productId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM attribute_sets WHERE product_id = ?'
        );
        $stmt->execute([$productId]);
        $sets = $stmt->fetchAll();

        $result = [];
        foreach ($sets as $set) {
            $attribute = $this->createAttribute($set, $productId);
            $result[] = [
                'id' => $attribute->getId(),
                'name' => $attribute->getName(),
                'type' => $attribute->getType(),
                'items' => $attribute->getItems(),
            ];
        }

        return $result;
    }

    private function createAttribute(
        array $set,
        string $productId
    ): AbstractAttribute {
        $items = $this->getItems($set['id'], $productId);
        return AttributeFactory::create(
            $set['id'],
            $set['name'],
            $set['type'],
            $items
        );
    }

    private function getItems(
        string $attributeSetId,
        string $productId
    ): array {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM attributes 
             WHERE attribute_set_id = ? AND product_id = ?'
        );
        $stmt->execute([$attributeSetId, $productId]);
        return $stmt->fetchAll();
    }
}