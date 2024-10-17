<?php

namespace App\Test\Controller;

use App\Enum\Image;
use App\Factory\CarFactory;
use App\Factory\CarImageFactory;
use App\Service\ImageManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class CarImageControllerTest extends WebTestCase
{
    use Factories;

    private ImageManager $imageManager;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->imageManager = static::getContainer()->get(ImageManager::class);
    }

    public function testDownload(): void
    {
        $path = sprintf('%s%s', self::$kernel->getProjectDir(), Image::Bird->value);

        $carImage = CarImageFactory::createOne([
            'imageFile' => $this->imageManager->createTemporyAndUploadedFile($path),
        ]);

        $this->client->request('GET', sprintf('/car-image/download/%s', $carImage->getId()));

        self::assertResponseStatusCodeSame(200);
    }

    public function testProcess(): void
    {
        $car = CarFactory::createOne();

        $path = sprintf('%s%s', self::$kernel->getProjectDir(), Image::Bird->value);

        $this->client->request(
            'POST',
            sprintf('/car-image/process/car/%s', $car->getId()),
            [],
            [
                'car' => [
                    'images' => [
                        $this->imageManager->createTemporyAndUploadedFile($path),
                    ],
                ],
            ],
            ['CONTENT_TYPE' => 'multipart/form-data']
        );

        self::assertResponseStatusCodeSame(200);
    }

    public function testRevert(): void
    {
        $carImage = CarImageFactory::createOne();

        $this->client->request(
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
        $carImage = CarImageFactory::createOne();

        $this->client->request('DELETE', sprintf('/car-image/%s/remove', $carImage->getId()));

        CarImageFactory::assert()->notExists(['id' => $carImage->getId()]);
    }
}
