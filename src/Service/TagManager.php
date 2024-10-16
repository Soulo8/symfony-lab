<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Illuminate\Support\Collection;

final class TagManager
{
    public function __construct(
        private TagRepository $tagRepository,
    ) {
    }

    /**
     * @return array<array<int|string>>
     */
    public function getSubTagsGroupByTag(): array
    {
        $subTags = $this->tagRepository->findWithParentOrderedByName();

        /**
         * @var array<array<int|string>> $groupedSubTags
         */
        $groupedSubTags = collect($subTags)
            ->groupBy(static function (Tag $tag): int {
                return $tag->getParent()->getId();
            })
            ->map(static function (Collection $group): array {
                return $group->map(static function (Tag $tag) {
                    return [
                        'id' => $tag->getId(),
                        'name' => $tag->getName(),
                    ];
                })->all();
            })
            ->all();

        return $groupedSubTags;
    }
}
