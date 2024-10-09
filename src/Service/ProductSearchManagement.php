<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductSearchManagement
{
    public function __construct(
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function buildForm(): FormInterface
    {
        return $this->formFactory->createBuilder(FormType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ])
            ->add('name', TextType::class, [
                'label' => 'name',
                'required' => false,
            ])
            ->getForm();
    }

    public function addConditions(QueryBuilder $qb, Request $request): QueryBuilder
    {
        $allData = $request->query->all();
        if (empty($request->query->all()) || !array_key_exists('form', $allData)) {
            return $qb;
        }

        $data = $allData['form'];
        if (null !== $data) {
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name', '%'.$data['name'].'%');
        }

        return $qb;
    }
}
