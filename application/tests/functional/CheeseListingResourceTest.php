<?php

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\HttpFoundation\Response;

class CheeseListingResourceTest extends ApiTestCase
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

        $response = $client->request('POST', '/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'fabelard@example.com',
                'password' => '123456',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $cheesyData = [
            'title' => 'Mystery cheese... kinda green',
            'description' => 'What mysteries does it hold?',
            'price' => 5000,
        ];
        $authenticatedUserIri = $response->getHeaders()['location'][0];

        $client->request('POST', '/api/cheeses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $cheesyData + ['owner' => $authenticatedUserIri],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }
}
