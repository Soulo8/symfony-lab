<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

abstract class AbstractTest extends ApiTestCase
{
    public function setUp(): void
    {
        self::bootKernel();
    }
}
