<?php

namespace App\Test\Controller;

use App\Factory\CarFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CarControllerTest extends WebTestCase
{
    use Factories, ResetDatabase;

    public function testIndex(): void
    {
        $client = static::createClient();

        $translator = static::getContainer()->get(TranslatorInterface::class);

        $client->request('GET', '/car');

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains($translator->trans('cars'));
    }

    public function testNew(): void
    {
        $client = static::createClient();

        $client->request('GET', '/car/new');

        self::assertResponseStatusCodeSame(200);

        $client->submitForm('save', [
            'car[name]' => 'Testing',
        ]);

        self::assertResponseRedirects('/car', 303);

        CarFactory::assert()->exists(['name' => 'Testing']);
    }

    public function testEdit(): void
    {
        $client = static::createClient();

        $car = CarFactory::createOne();

        $client->request('GET', sprintf('/car/%s/edit', $car->getId()));

        $client->submitForm('save', [
            'car[name]' => 'Something New',
        ]);

        self::assertResponseRedirects('/car', 303);

        CarFactory::assert()->exists(['name' => 'Something New']);
    }

    public function testRemove(): void
    {
        $client = static::createClient();

        $car = CarFactory::createOne();

        $client->request('GET', sprintf('/car/%s/edit', $car->getId()));
        $client->submitForm('delete');

        self::assertResponseRedirects('/car', 303);
        CarFactory::assert()->notExists(['id' => $car->getId()]);
    }
}
