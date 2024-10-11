<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Tag>
 */
class TagFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Tag::class;
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

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->text(10),
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Tag $tag): void {})
        ;
    }
}
