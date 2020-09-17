<?php

namespace App\Tests\Functional;

use App\Entity\CheeseListing;
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

        $user = $this->createUser('cheese.lover@example.com', '123456');

        $this->login($client, $user->getEmail(), '123456');

        $cheesyData = [
            'title' => 'Mystery cheese... kinda green',
            'description' => 'What mysteries does it hold?',
            'price' => 5000,
        ];

        $client->request('POST', '/api/cheeses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $cheesyData + ['owner' => 'api/users/'.$user->getId()],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testUpdateCheeseListingWithOwner()
    {
        $client = self::createClient();

        $user = $this->createUser('cheese.lover@example.com', '123456');

        $this->login($client, $user->getEmail(), '123456');

        $cheeseListing = new CheeseListing('Block of cheddar');
        $cheeseListing->setOwner($user);
        $cheeseListing->setPrice(1000);
        $cheeseListing->setDescription('mmmhh');

        $em = $this->getEntityManager();
        $em->persist($cheeseListing);
        $em->flush();

        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['description' => 'Yeah'],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUpdateCheeseListingWithAnotherUser()
    {
    }
}
