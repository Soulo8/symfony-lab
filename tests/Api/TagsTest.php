<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Tag;
use App\Factory\TagFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Zenstruck\Foundry\Test\Factories;

class TagsTest extends ApiTestCase
{
    use Factories;

    private EntityManagerInterface $entityManager;
    private HttpClientInterface $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    public function testGetCollection(): void
    {
        TagFactory::createMany(2);

        $response = $this->client->request('GET', '/api/tags');

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertMatchesResourceCollectionJsonSchema(Tag::class);
    }

    public function testCreateTag(): void
    {
        $response = $this->client->request('POST', '/api/tags', [
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
        $tagWithParent = TagFactory::createOne([
            'parent' => TagFactory::createOne(),
        ]);

        $response = $this->client->request('POST', '/api/tags', [
            'json' => [
                'name' => 'New tag',
                'parent' => sprintf('%s%s', '/api/tags/', $tagWithParent->getId()),
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
    }

    public function testGetCollectionDeleted(): void
    {
        TagFactory::createMany(2, ['deletedAt' => new \DateTime()]);

        $response = $this->client->request('GET', '/api/tags/deleted');

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertMatchesResourceCollectionJsonSchema(Tag::class);
        self::assertEquals(2, $response->toArray()['totalItems']);
    }

    public function testUpdateTag(): void
    {
        TagFactory::createOne(['name' => 'A tag']);

        $iri = $this->findIriBy(Tag::class, ['name' => 'A tag']);

        $this->client->request('PATCH', $iri, [
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
        $deletedAt = new \DateTime();
        TagFactory::createOne(['name' => 'A tag', 'deletedAt' => $deletedAt]);

        $this->entityManager->getFilters()->disable('softdeleteable');
        $iri = $this->findIriBy(Tag::class, ['name' => 'A tag']);
        $this->entityManager->getFilters()->enable('softdeleteable');

        $this->client->request('PATCH', sprintf('%s%s', $iri, '/restore'), [
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
        TagFactory::createOne(['name' => 'A tag']);

        $iri = $this->findIriBy(Tag::class, ['name' => 'A tag']);

        $this->client->request('DELETE', $iri);

        self::assertResponseStatusCodeSame(204);
        TagFactory::assert()->notExists(['name' => 'A tag', 'deletedAt' => null]);
    }
}
