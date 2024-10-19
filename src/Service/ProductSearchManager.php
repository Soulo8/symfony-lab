<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

final class ProductSearchManager
{
    public function addFilters(QueryBuilder $qb, Request $request): QueryBuilder
    {
        $all = $request->query->all();
        if (!array_key_exists('form', $all)) {
            return $qb;
        }

        $data = $all['form'];

        if (array_key_exists('name', $data) && '' !== $data['name']) {
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name', '%'.$data['name'].'%');
        }

        if (array_key_exists('subTag', $data) && '' !== $data['subTag']) {
            $qb->innerJoin('p.tags', 't')
                ->andWhere('t.id = :tag')
                ->setParameter('tag', $data['subTag']);
        } elseif (array_key_exists('tag', $data) && '' !== $data['tag']) {
            $qb->innerJoin('p.tags', 't')
                ->andWhere($qb->expr()->orX(
                    't.id = :tag',
                    't.parent = :tag'
                ))
                ->setParameter('tag', $data['tag']);
        }

        return $qb;
    }
}
