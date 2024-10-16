<?php

namespace App\Test\Controller;

use App\Factory\CarFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Foundry\Test\Factories;

class CarControllerTest extends WebTestCase
{
    use Factories;

    private KernelBrowser $client;
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/car');

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains($this->translator->trans('cars'));
    }

    public function testNew(): void
    {
        $this->client->request('GET', '/car/new');

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('save', [
            'car[name]' => 'Testing',
        ]);

        self::assertResponseRedirects('/car', 303);

        CarFactory::assert()->exists(['name' => 'Testing']);
    }

    public function testEdit(): void
    {
        $car = CarFactory::createOne();

        $this->client->request('GET', sprintf('/car/%s/edit', $car->getId()));

        $this->client->submitForm('save', [
            'car[name]' => 'Something New',
        ]);

        self::assertResponseRedirects('/car', 303);

        CarFactory::assert()->exists(['name' => 'Something New']);
    }

    public function testRemove(): void
    {
        $car = CarFactory::createOne();

        $this->client->request('GET', sprintf('/car/%s/edit', $car->getId()));
        $this->client->submitForm('delete');

        self::assertResponseRedirects('/car', 303);
        CarFactory::assert()->notExists(['id' => $car->getId()]);
    }
}
