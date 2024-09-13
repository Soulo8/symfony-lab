<?php

namespace App\Repository;

use App\Entity\ProductImage;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Sortable\Entity\Repository\SortableRepository;

/**
 * @extends SortableRepository
 */
class ProductImageRepository extends SortableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductImage::class);
    }
}
