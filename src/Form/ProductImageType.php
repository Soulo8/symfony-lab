<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ProductImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<ProductImage>
 */
final class ProductImageType extends AbstractType
{
    /**
     * @param array<string, mixed> $options
     */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        $builder->add('id', HiddenType::class, ['mapped' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductImage::class,
        ]);
    }
}
