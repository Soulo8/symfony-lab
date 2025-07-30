<?php

namespace App\Tests\Api;

use App\Entity\Tag;
use App\Factory\TagFactory;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TagsTest extends AbstractTest
{
    use Factories;
    use ResetDatabase;

    public function testGetCollection(): void
    {
        $client = static::createClient();

        TagFactory::createMany(2);

        $client->request('GET', '/api/tags');

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertMatchesResourceCollectionJsonSchema(Tag::class);
    }

    public function testCreateTag(): void
    {
        $client = static::createClient();

        $response = $client->request('POST', '/api/tags', [
            'json' => [
                'name' => 'New tag',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertMatchesRegularExpression('~^/api/tags/\d+$~', $response->toArray()['@id']);
        self::assertMatchesResourceItemJsonSchema(Tag::class);
        TagFactory::assert()->exists(['name' => 'New tag']);
    }

    public function testCreateInvalidTag(): void
    {
        $client = static::createClient();

        $tagWithParent = TagFactory::createOne([
            'parent' => TagFactory::createOne(),
        ]);

        $client->request('POST', '/api/tags', [
            'json' => [
                'name' => 'New tag',
                'parent' => sprintf('%s%d', '/api/tags/', $tagWithParent->getId()),
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
    }

    public function testGetCollectionDeleted(): void
    {
        $client = static::createClient();

        TagFactory::createMany(2, ['deletedAt' => new \DateTime()]);

        $response = $client->request('GET', '/api/tags/deleted');

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertMatchesResourceCollectionJsonSchema(Tag::class);
        self::assertEquals(2, $response->toArray()['totalItems']);
    }

    public function testUpdateTag(): void
    {
        $client = static::createClient();

        TagFactory::createOne(['name' => 'A tag']);

        $iri = $this->findIriBy(Tag::class, ['name' => 'A tag']);

        $client->request('PATCH', $iri, [
            'json' => [
                'name' => 'Update tag',
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
        TagFactory::assert()->exists(['name' => 'Update tag']);
    }

    public function testRestoreTag(): void
    {
        $client = static::createClient();

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $deletedAt = new \DateTime();
        TagFactory::createOne(['name' => 'A tag', 'deletedAt' => $deletedAt]);

        $entityManager->getFilters()->disable('softdeleteable');
        $iri = $this->findIriBy(Tag::class, ['name' => 'A tag']);
        $entityManager->getFilters()->enable('softdeleteable');

        $client->request('PATCH', sprintf('%s%s', $iri, '/restore'), [
            'json' => [],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
        TagFactory::assert()->exists(['name' => 'A tag', 'deletedAt' => null]);
    }

    public function testDeleteTag(): void
    {
        $client = static::createClient();

        TagFactory::createOne(['name' => 'A tag']);

        $iri = $this->findIriBy(Tag::class, ['name' => 'A tag']);

        $client->request('DELETE', $iri);

        self::assertResponseStatusCodeSame(204);
        TagFactory::assert()->notExists(['name' => 'A tag', 'deletedAt' => null]);
    }
}
