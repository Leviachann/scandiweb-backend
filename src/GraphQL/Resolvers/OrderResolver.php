<?php

namespace App\GraphQL\Resolvers;

use App\Database\Connection;

class OrderResolver
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance()->getPdo();
    }

    public function createOrder(array $items): int
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO orders (created_at) VALUES (NOW())'
            );
            $stmt->execute();
            $orderId = (int) $this->pdo->lastInsertId();

            foreach ($items as $item) {
                $stmt = $this->pdo->prepare('
                    INSERT INTO order_items 
                    (order_id, product_id, quantity, selected_attributes)
                    VALUES (?, ?, ?, ?)
                ');
                $stmt->execute([
                    $orderId,
                    $item['productId'],
                    $item['quantity'],
                    json_encode($item['selectedAttributes'] ?? [])
                ]);
            }

            $this->pdo->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}