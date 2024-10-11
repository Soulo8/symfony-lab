<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProductImage;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sortable\Entity\Repository\SortableRepository;

final class ProductImageRepository extends SortableRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        $classMetadata = $em->getClassMetadata(ProductImage::class);
        parent::__construct($em, $classMetadata);
    }
}
