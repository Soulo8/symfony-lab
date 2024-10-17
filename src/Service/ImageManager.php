<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

final class ImageManager
{
    public function __construct(
        private KernelInterface $kernel,
    ) {
    }

    public function createTemporyAndUploadedFile(string $path): UploadedFile
    {
        $tmpPath = $this->createTempory($path);

        return $this->createUploadedFile($tmpPath);
    }

    public function randomImage(): string
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

    private function createTempory(string $path): string
    {
        $basename = basename($path);
        $tmpPath = sprintf(
            '%s/%s',
            sys_get_temp_dir(),
            $this->generateRandomFilename($basename)
        );
        copy($path, $tmpPath);

        return $tmpPath;
    }

    private function createUploadedFile(string $path): UploadedFile
    {
        return new UploadedFile(
            $path,
            basename($path),
            mime_content_type($path),
            null,
            true
        );
    }

    private function generateRandomFilename(string $path): string
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $randomString = bin2hex(random_bytes(10));

        return sprintf('%s-%s.%s', $filename, $randomString, $extension);
    }
}
