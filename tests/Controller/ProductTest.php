<?php

namespace App\Tests\Controller;

use App\Enum\Image;
use App\Enum\Path;
use App\Factory\ProductFactory;
use App\Factory\ProductImageFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Foundry\Test\Factories;

class ProductTest extends WebTestCase
{
    use Factories;

    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/product');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testNew(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/product/new');

        $this->assertResponseStatusCodeSame(200);

        $buttonCrawlerNode = $crawler->selectButton('save');
        $form = $buttonCrawlerNode->form();

        $filePath = sprintf('%s%s', self::$kernel->getProjectDir(), Image::Landscape->value);
        $uploadedFile = new UploadedFile($filePath, 'landscape.jpg', 'image/jpeg', null);

        $form['product[name]'] = 'Mon produit';
        $client->submit($form, [
            'product[imageFile][file]' => $uploadedFile,
            'product[newImages][0]' => $uploadedFile,
        ]);

        $this->assertResponseRedirects('/product', 303);
    }

    public function testEdit(): void
    {
        $client = static::createClient();
        $filePath = sprintf('%s%s', self::$kernel->getProjectDir(), Image::Landscape->value);

        $product = ProductFactory::createOne([
            'imageFile' => new File($filePath),
            'images' => [ProductImageFactory::createOne(['imageFile' => new File($filePath)])],
        ]);

        $crawler = $client->request('GET', '/product/'.$product->getId().'/edit');

        $this->assertResponseStatusCodeSame(200);

        $buttonCrawlerNode = $crawler->selectButton('save');
        $form = $buttonCrawlerNode->form();

        $form['product[name]'] = 'Mon produit modifié';
        $client->submit($form, [
            'product[newImages][0]' => $product->getImages()->first()->getImageFile(),
        ]);

        $this->assertResponseRedirects('/product', 303);
    }

    public function testDelete(): void
    {
        $client = static::createClient();

        $product = ProductFactory::createOne();

        $crawler = $client->request('GET', '/product/'.$product->getId().'/edit');

        $buttonCrawlerNode = $crawler->selectButton('delete');
        $form = $buttonCrawlerNode->form();
        $client->submit($form);

        $this->assertResponseRedirects('/product', 303);
        ProductFactory::assert()->notExists(['id' => $product->getId()]);
    }

    protected function tearDown(): void
    {
        $projectDir = self::$kernel->getProjectDir();

        parent::tearDown();

        $filesystem = new Filesystem();
        $uploadDir = sprintf('%s%s', $projectDir, Path::FOLDER_UPLOADS_TEST->value);

        if ($filesystem->exists($uploadDir)) {
            $files = glob($uploadDir.'/*');
            foreach ($files as $file) {
                if ('.gitignore' !== basename($file)) {
                    $filesystem->remove($file);
                }
            }
        }
    }
}
