<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 */
final class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * @return array<Tag>
     */
    public function findWithParentOrderedByName(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.parent IS NOT NULL')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Tag>
     */
    public function findRandomRangeWithParent(int $min, int $max): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.parent IS NOT NULL')
            ->orderBy('RAND()')
            ->setMaxResults(random_int($min, $max))
            ->getQuery()
            ->getResult();
    }

    public function findWithoutParentQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->where('t.parent IS NULL');
    }

    public function findChildrensOfParentQueryBuilder(Tag $parent): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->where('t.parent = :parent')
            ->setParameter('parent', $parent);
    }

    /**
     * @return DoctrinePaginator<Tag>
     */
    public function getTagsDeleted(
        int $page = 1,
        int $itemsPerPage = 30,
    ): DoctrinePaginator {
        return new DoctrinePaginator(
            $this->createQueryBuilder('t')
                ->where('t.deletedAt IS NOT NULL')
                ->addCriteria(
                    Criteria::create()
                        ->setFirstResult(($page - 1) * $itemsPerPage)
                        ->setMaxResults($itemsPerPage)
                )
        );
    }
}
