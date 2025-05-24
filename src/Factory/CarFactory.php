<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Car;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Car>
 */
final class CarFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Car::class;
    }

    /**
     * @return array<string, \DateTime|string>
     */
    protected function defaults(): array
    {
        return [
            'createdAt' => self::faker()->dateTime(),
            'name' => self::faker()->text(255),
            'updatedAt' => self::faker()->dateTime(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Car $car): void {})
        ;
    }
}
