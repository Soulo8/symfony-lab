<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\ProductImage;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<ProductImage>
 */
class ProductImageFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return ProductImage::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'createdAt' => self::faker()->dateTime(),
            'position' => self::faker()->numberBetween(1, 32767),
            'updatedAt' => self::faker()->dateTime(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(ProductImage $productImage): void {})
        ;
    }
}
