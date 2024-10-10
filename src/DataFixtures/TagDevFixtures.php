<?php

namespace App\DataFixtures;

use App\Factory\TagFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class TagDevFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $tag = TagFactory::createOne(['name' => 'Tag 1']);
        TagFactory::createOne(['name' => 'Sous tag 1 de tag 1', 'parent' => $tag]);
        TagFactory::createOne(['name' => 'Sous tag 2 de tag 1', 'parent' => $tag]);

        $tag = TagFactory::createOne(['name' => 'Tag 2']);
        TagFactory::createOne(['name' => 'Sous tag 1 de tag 2', 'parent' => $tag]);
        TagFactory::createOne(['name' => 'Sous tag 2 de tag 2', 'parent' => $tag]);

        TagFactory::createMany(5);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }
}
