<?php

namespace App\Tests\Controller;

use App\Enum\Image;
use App\Enum\Path;
use App\Factory\ProductFactory;
use App\Factory\ProductImageFactory;
use App\Factory\TagFactory;
use App\Service\ImageManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProductTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testIndex(): void
    {
        $client = static::createClient();

        $translator = static::getContainer()->get(TranslatorInterface::class);

        $client->request('GET', '/product');

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains($translator->trans('products'));
    }

    public function testNew(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/product/new');

        self::assertResponseStatusCodeSame(200);

        $buttonCrawlerNode = $crawler->selectButton('save');
        $form = $buttonCrawlerNode->form();

        $tag = TagFactory::createOne();

        $dom = new \DOMDocument();
        $node = $dom->createElement('option', $tag->getName());
        $node->setAttribute('value', strval($tag->getId()));
        /** @var ChoiceFormField $fieldTags */
        $fieldTags = $form->get('product[tags]');
        $fieldTags->addChoice($node);

        // To display the data contained in the form
        // dump($form->all());

        $filePath = sprintf('%s%s', self::$kernel->getProjectDir(), Image::Landscape->value);
        $uploadedFile = new UploadedFile($filePath, 'landscape.jpg', 'image/jpeg', null);

        $client->submit($form, [
            'product[name]' => 'Testing',
            'product[tags]' => [$tag->getId()],
            'product[imageFile][file]' => $uploadedFile,
            'product[newImages][0]' => $uploadedFile,
        ]);

        self::assertResponseRedirects('/product', 303);

        ProductFactory::assert()->exists(['name' => 'Testing']);
    }

    public function testEdit(): void
    {
        $client = static::createClient();

        $filePath = sprintf('%s%s', self::$kernel->getProjectDir(), Image::Landscape->value);

        $product = ProductFactory::createOne([
            'imageFile' => new File($filePath),
            'images' => [ProductImageFactory::createOne(['imageFile' => new File($filePath)])],
        ]);

        $client->request('GET', sprintf('/product/%s/edit', $product->getId()));

        self::assertResponseStatusCodeSame(200);

        $client->submitForm('save', [
            'product[name]' => 'Something New',
            'product[newImages][0]' => $product->getImages()->first()->getImageFile(),
        ]);

        self::assertResponseRedirects('/product', 303);

        ProductFactory::assert()->exists(['name' => 'Something New']);
    }

    public function testDelete(): void
    {
        $client = static::createClient();

        $product = ProductFactory::createOne();

        $client->request('GET', sprintf('/product/%s/edit', $product->getId()));
        $client->submitForm('delete');

        self::assertResponseRedirects('/product', 303);
        ProductFactory::assert()->notExists(['id' => $product->getId()]);
    }

    public function testDownloadImage(): void
    {
        $client = static::createClient();

        $imageManager = static::getContainer()->get(ImageManager::class);

        $path = sprintf('%s%s', self::$kernel->getProjectDir(), Image::Bird->value);

        $image2 = ProductImageFactory::createOne([
            'imageFile' => $imageManager->createTemporyAndUploadedFile($path),
        ]);

        ProductFactory::createOne([
            'imageFile' => $imageManager->createTemporyAndUploadedFile($path),
            'images' => [$image2],
        ]);

        $client->request('GET', sprintf('/product/download-image/%s', $image2->getId()));

        self::assertResponseStatusCodeSame(200);
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
