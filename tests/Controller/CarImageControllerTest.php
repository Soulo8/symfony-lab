<?php

namespace App\Test\Controller;

use App\Enum\Image;
use App\Factory\CarFactory;
use App\Factory\CarImageFactory;
use App\Service\ImageManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CarImageControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testDownload(): void
    {
        $client = static::createClient();

        $imageManager = static::getContainer()->get(ImageManager::class);

        $path = sprintf('%s%s', self::$kernel->getProjectDir(), Image::Bird->value);

        $carImage = CarImageFactory::createOne([
            'imageFile' => $imageManager->createTemporyAndUploadedFile($path),
        ]);

        $client->request('GET', sprintf('/car-image/download/%s', $carImage->getId()));

        self::assertResponseStatusCodeSame(200);
    }

    public function testProcess(): void
    {
        $client = static::createClient();

        $imageManager = static::getContainer()->get(ImageManager::class);

        $car = CarFactory::createOne();

        $path = sprintf('%s%s', self::$kernel->getProjectDir(), Image::Bird->value);

        $client->request(
            'POST',
            sprintf('/car-image/process/car/%s', $car->getId()),
            [],
            [
                'car' => [
                    'images' => [
                        $imageManager->createTemporyAndUploadedFile($path),
                    ],
                ],
            ],
            ['CONTENT_TYPE' => 'multipart/form-data']
        );

        self::assertResponseStatusCodeSame(200);
    }

    public function testRevert(): void
    {
        $client = static::createClient();

        $carImage = CarImageFactory::createOne();

        $client->request(
            'DELETE',
            '/car-image/revert',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($carImage->getId())
        );

        CarImageFactory::assert()->notExists(['id' => $carImage->getId()]);
    }

    public function testRemove(): void
    {
        $client = static::createClient();

        $carImage = CarImageFactory::createOne();

        $client->request('DELETE', sprintf('/car-image/%s/remove', $carImage->getId()));

        CarImageFactory::assert()->notExists(['id' => $carImage->getId()]);
    }
}
