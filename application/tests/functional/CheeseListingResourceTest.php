<?php

namespace App\Tests\Functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\HttpFoundation\Response;

class CheeseListingResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateCheeseListingWithAnonymousUser()
    {
        $client = self::createClient();
        $client->request('POST', '/api/cheeses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateCheeseListingWithAuthenticatedUser()
    {
        $client = self::createClient();

        $authenticatedUserIri = $this->login($client, 'fabelard@example.com', '123456');

        $cheesyData = [
            'title' => 'Mystery cheese... kinda green',
            'description' => 'What mysteries does it hold?',
            'price' => 5000,
        ];

        $client->request('POST', '/api/cheeses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $cheesyData + ['owner' => $authenticatedUserIri],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }
}
