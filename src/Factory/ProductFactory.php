<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Product;
use App\Enum\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Product>
 */
class ProductFactory extends PersistentProxyObjectFactory
{
    public function __construct(
        private KernelInterface $kernel,
    ) {
    }

    public static function class(): string
    {
        return Product::class;
    }

    public function generateRandomFilename(string $originalFilename): string
    {
        $filename = pathinfo($originalFilename, PATHINFO_FILENAME);
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $randomString = bin2hex(random_bytes(10));

        return sprintf('%s-%s.%s', $filename, $randomString, $extension);
    }

    protected function defaults(): array|callable
    {
        return [
            'createdAt' => self::faker()->dateTime(),
            'name' => self::faker()->text(20),
            'updatedAt' => self::faker()->dateTime(),
            'imageFile' => $this->buildImage(),
            'images' => [
                ProductImageFactory::createOne([
                    'imageFile' => $this->buildImage(),
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

    private function buildImage(): UploadedFile
    {
        $imagePath = $this->randomImage();
        $basename = basename($imagePath);
        $tmpImagePath = $this->buildTmpImagePath(
            $this->generateRandomFilename($basename)
        );
        copy($imagePath, $tmpImagePath);

        return new UploadedFile(
            $tmpImagePath,
            $basename,
            mime_content_type($tmpImagePath),
            null,
            true
        );
    }

    private function randomImage(): string
    {
        $images = [
            Image::Bird->value,
            Image::Car->value,
            Image::Cat->value,
            Image::Landscape->value,
            Image::Ship->value,
        ];

        return sprintf(
            '%s%s',
            $this->kernel->getProjectDir(),
            $images[array_rand($images)]
        );
    }

    private function buildTmpImagePath(string $filename): string
    {
        return sprintf(
            '%s/%s',
            sys_get_temp_dir(),
            $filename
        );
    }
}
