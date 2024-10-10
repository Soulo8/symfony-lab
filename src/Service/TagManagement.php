<?php

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Illuminate\Support\Collection;

class TagManagement
{
    public function __construct(
        private TagRepository $tagRepository,
    ) {
    }

    /**
     * @return array<string>
     */
    public function getSubTagsGroupByTag(): array
    {
        $subTags = $this->tagRepository->findWithParentOrderedByName();
        $subTagsGroupByTag = collect($subTags)
            ->groupBy(function (Tag $tag): int {
                return $tag->getParent()->getId();
            })
            ->map(function (Collection $group): Collection {
                return $group->map(function (Tag $tag) {
                    return [
                        'id' => $tag->getId(),
                        'name' => $tag->getName(),
                    ];
                });
            })
            ->all();

        return $subTagsGroupByTag;
    }
}
