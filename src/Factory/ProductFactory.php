<?php

namespace App\Factory;

use App\Entity\Product;
use App\Enum\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Product>
 */
final class ProductFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(
        private KernelInterface $kernel,
    ) {
    }

    public static function class(): string
    {
        return Product::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'createdAt' => self::faker()->dateTime(),
            'name' => self::faker()->text(20),
            'updatedAt' => self::faker()->dateTime(),
            'imageFile' => $this->buildImage(),
            'images' => [ProductImageFactory::createOne(['imageFile' => $this->buildImage()])],
        ];
    }

    private function buildImage(): UploadedFile
    {
        $imagePath = $this->randomImage();
        $basename = basename($imagePath);
        $tmpImagePath = $this->buildTmpImagePath($this->generateRandomFilename($basename));
        copy($imagePath, $tmpImagePath);
        $image = new UploadedFile($tmpImagePath, $basename, mime_content_type($tmpImagePath), null, true);

        return $image;
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

        return sprintf('%s%s', $this->kernel->getProjectDir(), $images[array_rand($images)]);
    }

    private function buildTmpImagePath(string $filename): string
    {
        return sprintf(
            '%s/%s',
            sys_get_temp_dir(),
            $filename
        );
    }

    public function generateRandomFilename(string $originalFilename): string
    {
        $filename = pathinfo($originalFilename, PATHINFO_FILENAME);
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $randomString = bin2hex(random_bytes(10));

        return sprintf('%s-%s.%s', $filename, $randomString, $extension);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Product $product): void {})
        ;
    }
}
