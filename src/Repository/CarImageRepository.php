<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CarImage;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sortable\Entity\Repository\SortableRepository;

final class CarImageRepository extends SortableRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        $classMetadata = $em->getClassMetadata(CarImage::class);
        parent::__construct($em, $classMetadata);
    }
}
