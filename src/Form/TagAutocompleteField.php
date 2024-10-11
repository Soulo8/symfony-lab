<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
final class TagAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Tag::class,
            'query_builder' => static function (
                TagRepository $tagRepository,
            ): QueryBuilder {
                return $tagRepository->createQueryBuilder('t')
                    ->orderBy('t.name', 'ASC');
            },
            'searchable_fields' => ['name'],
            'label' => 'tags',
            'choice_label' => 'name',
            'multiple' => true,
            'group_by' => 'parent.name',
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
