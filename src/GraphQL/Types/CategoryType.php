<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Resolvers\ProductResolver;
use App\GraphQL\TypeRegistry;

class CategoryType extends ObjectType
{
    public function __construct()
    {
        $productResolver = new ProductResolver();

        parent::__construct([
            'name' => 'Category',
            'fields' => fn() => [
                'id' => Type::int(),
                'name' => Type::string(),
                'products' => [
                    'type' => Type::listOf(
                        TypeRegistry::get(ProductType::class)
                    ),
                    'resolve' => fn($category) =>
                        $productResolver->getByCategoryId($category['id'])
                ],
            ]
        ]);
    }
}