<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ProductSearch;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class CarSearchManager
{
    public function __construct(
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function buildForm(): FormInterface
    {
        return $this->formFactory->createBuilder(
            FormType::class,
            new ProductSearch(),
            [
                'method' => 'GET',
                'csrf_protection' => false,
            ]
        )
            ->add('name', TextType::class, [
                'label' => 'name',
                'required' => false,
            ])
            ->getForm();
    }

    public function addFilters(QueryBuilder $qb, Request $request): QueryBuilder
    {
        $all = $request->query->all();
        if ([] === $all || !array_key_exists('form', $all)) {
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
