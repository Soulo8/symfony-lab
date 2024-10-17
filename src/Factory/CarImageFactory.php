<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\CarImage;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<CarImage>
 */
final class CarImageFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return CarImage::class;
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
            // ->afterInstantiate(function(CarImage $carImage): void {})
        ;
    }
}
