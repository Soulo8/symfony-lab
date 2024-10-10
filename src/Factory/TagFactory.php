<?php

namespace App\Factory;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Tag>
 */
final class TagFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Tag::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->text(10),
        ];
    }

    /**
     * @return array<Tag>
     */
    public static function randomRangeWithParent(int $min, int $max): array
    {
        /** @var TagRepository $tagRepository */
        $tagRepository = self::repository();

        return $tagRepository->findRandomRangeWithParent($min, $max);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Tag $tag): void {})
        ;
    }
}
