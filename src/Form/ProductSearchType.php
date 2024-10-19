<?php

declare(strict_types=1);

namespace App\Form;

use App\Dto\ProductSearch;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductSearchType extends AbstractType
{
    public function __construct(
        private TagRepository $tagRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        $builder
            ->setMethod('GET')
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
        ;
    }

    public function onPreSetData(PreSetDataEvent $event): void
    {
        $form = $event->getForm();
        $this->addSubTagWithoutChoice($form);
    }

    public function onPreSubmit(PreSubmitEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();

        $tag = $this->tagRepository->find($data['tag']);

        if (null === $tag || $tag->getChildrens()->isEmpty()) {
            $this->addSubTagWithoutChoice($form);
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

    public function getBlockPrefix(): string
    {
        return 'form';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductSearch::class,
            'csrf_protection' => false,
        ]);
    }

    private function addSubTagWithoutChoice(FormInterface $form): FormInterface
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
