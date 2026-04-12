<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = new PDO(
    'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8mb4',
    $_ENV['DB_USER'],
    $_ENV['DB_PASS']
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$json = file_get_contents(__DIR__ . '/data.json');
$data = json_decode($json, true)['data'];

$categoryIds = [];
foreach ($data['categories'] as $category) {
    if ($category['name'] === 'all') continue;
    $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (?)');
    $stmt->execute([$category['name']]);
    $categoryIds[$category['name']] = $pdo->lastInsertId();
}

foreach ($data['products'] as $product) {
    $categoryId = $categoryIds[$product['category']];

    $stmt = $pdo->prepare('
        INSERT INTO products (id, name, in_stock, description, category_id, brand)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([
        $product['id'],
        $product['name'],
        $product['inStock'] ? 1 : 0,
        $product['description'],
        $categoryId,
        $product['brand']
    ]);

    foreach ($product['gallery'] as $imageUrl) {
        $stmt = $pdo->prepare('INSERT INTO product_gallery (product_id, image_url) VALUES (?, ?)');
        $stmt->execute([$product['id'], $imageUrl]);
    }

    foreach ($product['attributes'] as $attributeSet) {
        $stmt = $pdo->prepare('
            INSERT INTO attribute_sets (id, product_id, name, type)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([
            $attributeSet['id'],
            $product['id'],
            $attributeSet['name'],
            $attributeSet['type']
        ]);

        foreach ($attributeSet['items'] as $item) {
            $stmt = $pdo->prepare('
                INSERT INTO attributes (id, attribute_set_id, product_id, display_value, value)
                VALUES (?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $item['id'],
                $attributeSet['id'],
                $product['id'],
                $item['displayValue'],
                $item['value']
            ]);
        }
    }

    foreach ($product['prices'] as $price) {
        $stmt = $pdo->prepare('
            INSERT INTO prices (product_id, amount, currency_label, currency_symbol)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([
            $product['id'],
            $price['amount'],
            $price['currency']['label'],
            $price['currency']['symbol']
        ]);
    }
}

echo "Database seeded successfully!\n";