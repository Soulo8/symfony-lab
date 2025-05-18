<?php

namespace App\Tests\Controller;

use App\Enum\Image;
use App\Enum\Path;
use App\Factory\ProductFactory;
use App\Factory\ProductImageFactory;
use App\Factory\TagFactory;
use App\Service\ImageManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Foundry\Test\Factories;

class ProductTest extends WebTestCase
{
    use Factories;

    private ImageManager $imageManager;
    private KernelBrowser $client;
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $this->imageManager = $container->get(ImageManager::class);
        $this->translator = $container->get(TranslatorInterface::class);
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/product');

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains($this->translator->trans('products'));
    }

    public function testNew(): void
    {
        $crawler = $this->client->request('GET', '/product/new');

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

        $this->client->submit($form, [
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
        $filePath = sprintf('%s%s', self::$kernel->getProjectDir(), Image::Landscape->value);

        $product = ProductFactory::createOne([
            'imageFile' => new File($filePath),
            'images' => [ProductImageFactory::createOne(['imageFile' => new File($filePath)])],
        ]);

        $this->client->request('GET', sprintf('/product/%s/edit', $product->getId()));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('save', [
            'product[name]' => 'Something New',
            'product[newImages][0]' => $product->getImages()->first()->getImageFile(),
        ]);

        self::assertResponseRedirects('/product', 303);

        ProductFactory::assert()->exists(['name' => 'Something New']);
    }

    public function testDelete(): void
    {
        $product = ProductFactory::createOne();

        $this->client->request('GET', sprintf('/product/%s/edit', $product->getId()));
        $this->client->submitForm('delete');

        self::assertResponseRedirects('/product', 303);
        ProductFactory::assert()->notExists(['id' => $product->getId()]);
    }

    public function testDownloadImage(): void
    {
        $path = sprintf('%s%s', self::$kernel->getProjectDir(), Image::Bird->value);

        $image2 = ProductImageFactory::createOne([
            'imageFile' => $this->imageManager->createTemporyAndUploadedFile($path),
        ]);

        ProductFactory::createOne([
            'imageFile' => $this->imageManager->createTemporyAndUploadedFile($path),
            'images' => [$image2],
        ]);

        $this->client->request('GET', sprintf('/product/download-image/%s', $image2->getId()));

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
