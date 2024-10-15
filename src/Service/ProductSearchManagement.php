<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ProductSearch;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class ProductSearchManagement
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private TagRepository $tagRepository,
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
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'query_builder' => static function (
                    TagRepository $tagRepository,
                ): QueryBuilder {
                    return $tagRepository->findWithoutParentQueryBuilder()
                        ->orderBy('t.name', 'ASC');
                },
                'choice_label' => 'name',
                'required' => false,
                'label' => 'tag',
                'attr' => ['data-action' => 'change->tag#updateSubTags'],
            ])
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                [$this, 'onPreSetData']
            )
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                [$this, 'onPreSubmit']
            )
            ->getForm();
    }

    public function onPreSetData(PreSetDataEvent $event): void
    {
        $form = $event->getForm();
        $this->addSubTagDisabled($form);
    }

    public function onPreSubmit(PreSubmitEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();

        $tag = $this->tagRepository->find($data['tag']);

        if (null === $tag || $tag->getChildrens()->isEmpty()) {
            $this->addSubTagDisabled($form);
        } else {
            $form->add('subTag', EntityType::class, [
                'class' => Tag::class,
                'query_builder' => static function (
                    TagRepository $tagRepository,
                ) use ($tag): QueryBuilder {
                    return $tagRepository
                        ->findChildrensOfParentQueryBuilder($tag)
                        ->orderBy('t.name', 'ASC');
                },
                'choice_label' => 'name',
                'required' => false,
                'label' => 'sub_tag',
                'attr' => ['data-tag-target' => 'subTagList'],
            ]);
        }
    }

    public function addFilters(QueryBuilder $qb, Request $request): QueryBuilder
    {
        $all = $request->query->all();
        if ([] === $all || !array_key_exists('form', $all)) {
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

    private function addSubTagDisabled(FormInterface $form): FormInterface
    {
        $form->add('subTag', EntityType::class, [
            'class' => Tag::class,
            'choices' => [],
            'required' => false,
            'label' => 'sub_tag',
            'attr' => ['data-tag-target' => 'subTagList'],
        ]);

        return $form;
    }
}
