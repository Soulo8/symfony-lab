<?php

namespace App\Repository;

use App\Entity\ProductImage;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sortable\Entity\Repository\SortableRepository;

class ProductImageRepository extends SortableRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        $classMetadata = $em->getClassMetadata(ProductImage::class);
        parent::__construct($em, $classMetadata);
    }
}
