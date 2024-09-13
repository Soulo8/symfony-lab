<?php

namespace App\Tests\Controller;

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

    private const IMAGE_FIXTURE_PATH = '/../../src/DataFixtures/paysage.jpg';
    private const FOLDER_UPLOADS_TEST_PATH = '/../../uploads_test/products';

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

        $filePath = __DIR__.self::IMAGE_FIXTURE_PATH;
        $uploadedFile = new UploadedFile($filePath, 'paysage.jpg', 'image/jpeg', null);

        $form['product[name]'] = 'Mon produit';
        $form['product[imageFile][file]'] = $uploadedFile;
        $form['product[newImages][0]'] = $uploadedFile;
        $client->submit($form);

        $this->assertResponseRedirects('/product', 303);
    }

    public function testShow(): void
    {
        $client = static::createClient();

        $product = ProductFactory::createOne();

        $client->request('GET', '/product/'.$product->getId());

        $this->assertResponseStatusCodeSame(200);
    }

    public function testEdit(): void
    {
        $client = static::createClient();

        $filePath = __DIR__.self::IMAGE_FIXTURE_PATH;

        $product = ProductFactory::createOne([
            'imageFile' => new File($filePath),
            'images' => [ProductImageFactory::createOne(['imageFile' => new File($filePath)])],
        ]);

        $crawler = $client->request('GET', '/product/'.$product->getId().'/edit');

        $this->assertResponseStatusCodeSame(200);

        $buttonCrawlerNode = $crawler->selectButton('save');
        $form = $buttonCrawlerNode->form();

        $form['product[name]'] = 'Mon produit modifié';
        $form['product[newImages][0]'] = $product->getImages()->first()->getImageFile();
        $client->submit($form);

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
        parent::tearDown();

        $filesystem = new Filesystem();
        $uploadDir = __DIR__.self::FOLDER_UPLOADS_TEST_PATH;

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
