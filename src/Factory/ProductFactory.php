<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Product;
use App\Service\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Product>
 */
class ProductFactory extends PersistentProxyObjectFactory
{
    public function __construct(
        private ImageManager $imageManager,
    ) {
    }

    public static function class(): string
    {
        return Product::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'createdAt' => self::faker()->dateTime(),
            'name' => self::faker()->text(20),
            'updatedAt' => self::faker()->dateTime(),
            'imageFile' => $this->createImage(),
            'images' => [
                ProductImageFactory::createOne([
                    'imageFile' => $this->createImage(),
                ]),
            ],
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Product $product): void {})
        ;
    }

    private function createImage(): UploadedFile
    {
        $path = $this->imageManager->randomImage();

        return $this->imageManager->createTemporyAndUploadedFile($path);
    }
}
