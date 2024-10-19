<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

final class CarSearchManager
{
    public function addFilters(QueryBuilder $qb, Request $request): QueryBuilder
    {
        $all = $request->query->all();
        if (!array_key_exists('form', $all)) {
            return $qb;
        }

        $data = $all['form'];

        if (array_key_exists('name', $data) && '' !== $data['name']) {
            $qb->andWhere('c.name LIKE :name')
                ->setParameter('name', '%'.$data['name'].'%');
        }

        return $qb;
    }
}
