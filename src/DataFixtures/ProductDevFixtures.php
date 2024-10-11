<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\ProductFactory;
use App\Factory\TagFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ProductDevFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        ProductFactory::createMany(
            7,
            static function () {
                return ['tags' => [TagFactory::random(['parent' => null])]];
            }
        );

        ProductFactory::createMany(
            7,
            static function () {
                return ['tags' => TagFactory::randomRangeWithParent(2, 2)];
            }
        );

        $manager->flush();
    }

    /**
     * @return array<string>
     */
    public function getDependencies(): array
    {
        return [
            TagDevFixtures::class,
        ];
    }

    /**
     * @return array<string>
     */
    public static function getGroups(): array
    {
        return ['dev'];
    }
}
