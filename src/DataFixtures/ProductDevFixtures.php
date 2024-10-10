<?php

namespace App\DataFixtures;

use App\Factory\ProductFactory;
use App\Factory\TagFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductDevFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        ProductFactory::createMany(
            7,
            function () {
                return ['tags' => [TagFactory::random(['parent' => null])]];
            }
        );

        ProductFactory::createMany(
            7,
            function () {
                return ['tags' => TagFactory::randomRangeWithParent(2, 2)];
            }
        );

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TagDevFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }
}
