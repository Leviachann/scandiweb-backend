<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Resolvers\AttributeResolver;
use App\GraphQL\TypeRegistry;

class ProductType extends ObjectType
{
    public function __construct()
    {
        $attributeResolver = new AttributeResolver();

        parent::__construct([
            'name' => 'Product',
            'fields' => fn() => [
                'id' => Type::string(),
                'name' => Type::string(),
                'inStock' => [
                    'type' => Type::boolean(),
                    'resolve' => fn($product) => (bool) $product['in_stock']
                ],
                'description' => Type::string(),
                'brand' => Type::string(),
                'category_id' => Type::int(),
                'category' => [
                    'type' => Type::string(),
                    'resolve' => fn($product) => $product['category_name'] ?? null
                ],
                'gallery' => Type::listOf(Type::string()),
                'prices' => Type::listOf(
                    TypeRegistry::get(PriceType::class)
                ),
                'attributes' => [
                    'type' => Type::listOf(
                        TypeRegistry::get(AttributeSetType::class)
                    ),
                    'resolve' => fn($product) =>
                        $attributeResolver->getByProductId($product['id'])
                ],
            ]
        ]);
    }
}