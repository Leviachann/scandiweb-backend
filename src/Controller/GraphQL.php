<?php

namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;
use App\GraphQL\Types\CategoryType;
use App\GraphQL\Types\ProductType;
use App\GraphQL\Resolvers\CategoryResolver;
use App\GraphQL\Resolvers\ProductResolver;
use App\GraphQL\Resolvers\OrderResolver;
use App\GraphQL\TypeRegistry;
use RuntimeException;
use Throwable;

class GraphQL
{
    public static function handle(): string
    {
        try {
            $categoryResolver = new CategoryResolver();
            $productResolver = new ProductResolver();
            $orderResolver = new OrderResolver();

            $categoryType = TypeRegistry::get(CategoryType::class);
            $productType = TypeRegistry::get(ProductType::class);

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'categories' => [
                        'type' => Type::listOf($categoryType),
                        'resolve' => fn() => $categoryResolver->getAll()
                    ],
                    'category' => [
                        'type' => $categoryType,
                        'args' => [
                            'name' => Type::string()
                        ],
                        'resolve' => fn($root, $args) =>
                            $categoryResolver->getByName($args['name'])
                    ],
                    'products' => [
                        'type' => Type::listOf($productType),
                        'resolve' => fn() => $productResolver->getAll()
                    ],
                    'product' => [
                        'type' => $productType,
                        'args' => [
                            'id' => Type::string()
                        ],
                        'resolve' => fn($root, $args) =>
                            $productResolver->getById($args['id'])
                    ],
                    'productsByCategory' => [
                        'type' => Type::listOf($productType),
                        'args' => [
                            'categoryId' => Type::int()
                        ],
                        'resolve' => fn($root, $args) =>
                            $productResolver->getByCategoryId($args['categoryId'])
                    ],
                ],
            ]);

            $orderItemInputType = new InputObjectType([
                'name' => 'OrderItemInput',
                'fields' => [
                    'productId' => Type::nonNull(Type::string()),
                    'quantity' => Type::nonNull(Type::int()),
                    'selectedAttributes' => Type::string(),
                ],
            ]);

            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'createOrder' => [
                        'type' => Type::int(),
                        'args' => [
                            'items' => Type::nonNull(
                                Type::listOf($orderItemInputType)
                            ),
                        ],
                        'resolve' => fn($root, $args) =>
                            $orderResolver->createOrder($args['items'])
                    ],
                ],
            ]);

            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
                    ->setMutation($mutationType)
            );

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;

            $result = GraphQLBase::executeQuery(
                $schema,
                $query,
                null,
                null,
                $variableValues
            );
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}