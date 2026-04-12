<?php

namespace App\GraphQL\Resolvers;

use App\Database\Connection;

class ProductResolver
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance()->getPdo();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('
            SELECT p.*, c.name as category_name 
            FROM products p
            JOIN categories c ON p.category_id = c.id
        ');
        return array_map([$this, 'hydrateProduct'], $stmt->fetchAll());
    }

    public function getById(string $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT p.*, c.name as category_name 
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.id = ?
        ');
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if (!$product) return null;
        return $this->hydrateProduct($product);
    }

    public function getByCategoryId(int $categoryId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT p.*, c.name as category_name 
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.category_id = ?
        ');
        $stmt->execute([$categoryId]);
        $products = $stmt->fetchAll();
        return array_map([$this, 'hydrateProduct'], $products);
    }

    private function hydrateProduct(array $product): array
    {
        $product['gallery'] = $this->getGallery($product['id']);
        $product['prices'] = $this->getPrices($product['id']);
        $product['attributes'] = $this->getAttributes($product['id']);
        return $product;
    }

    private function getGallery(string $productId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT image_url FROM product_gallery WHERE product_id = ?'
        );
        $stmt->execute([$productId]);
        return array_column($stmt->fetchAll(), 'image_url');
    }

    private function getPrices(string $productId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM prices WHERE product_id = ?'
        );
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    private function getAttributes(string $productId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM attribute_sets WHERE product_id = ?'
        );
        $stmt->execute([$productId]);
        $sets = $stmt->fetchAll();

        foreach ($sets as &$set) {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM attributes 
                 WHERE attribute_set_id = ? AND product_id = ?'
            );
            $stmt->execute([$set['id'], $productId]);
            $set['items'] = $stmt->fetchAll();
        }

        return $sets;
    }
}