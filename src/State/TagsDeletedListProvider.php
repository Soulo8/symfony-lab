<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @implements ProviderInterface<Tag>
 */
final class TagsDeletedListProvider implements ProviderInterface
{
    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly Pagination $pagination,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param array<string, mixed>                                                   $uriVariables
     * @param array<string, mixed>|array{request?: Request, resource_class?: string} $context
     */
    public function provide(
        Operation $operation,
        array $uriVariables = [],
        array $context = [],
    ): Paginator {
        [$page, , $limit] = $this->pagination->getPagination(
            $operation,
            $context
        );

        $this->entityManager->getFilters()->disable('softdeleteable');

        return new Paginator(
            $this->tagRepository->getTagsDeleted($page, $limit)
        );
    }
}
