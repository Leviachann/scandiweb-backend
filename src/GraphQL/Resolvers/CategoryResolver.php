<?php

namespace App\GraphQL\Resolvers;

use App\Database\Connection;

class CategoryResolver
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance()->getPdo();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM categories');
        return $stmt->fetchAll();
    }

    public function getByName(string $name): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categories WHERE name = ?');
        $stmt->execute([$name]);
        return $stmt->fetch() ?: null;
    }
}