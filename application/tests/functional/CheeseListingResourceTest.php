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

        $authenticatedUser = $this->createUser('cheese.lover@example.com', '123456');
        $this->login($client, $authenticatedUser->getEmail(), '123456');

        $otherUser = $this->createUser('other.user@example.com', 'foo');

        $cheesyData = [
            'title' => 'Mystery cheese... kinda green',
            'description' => 'What mysteries does it hold?',
            'price' => 5000,
        ];

        $client->request('POST', '/api/cheeses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $cheesyData + ['owner' => 'api/users/' . $otherUser->getId()],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST, 'not passing the correct owner');

        $client->request('POST', '/api/cheeses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $cheesyData + ['owner' => 'api/users/' . $authenticatedUser->getId()],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $client->request('POST', '/api/cheeses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $cheesyData,
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testUpdateCheeseListing()
    {
        $client = self::createClient();

        $ownerUser = $this->createUser('cheese.lover@example.com', '123456');
        $this->login($client, $ownerUser->getEmail(), '123456');

        $cheeseListing = new CheeseListing('Block of cheddar');
        $cheeseListing->setOwner($ownerUser);
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

        $anotherUser = $this->createUser('camembert.lover@example.com', '123456');
        $this->login($client, $anotherUser->getEmail(), '123456');

        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['description' => 'Yeah'],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testGetCheeseListingCollection()
    {
        $client = self::createClient();
        $user = $this->createUser('cheeseplease@example.com', 'foo');

        $cheeseListing1 = new CheeseListing('cheese1');
        $cheeseListing1->setOwner($user);
        $cheeseListing1->setPrice(1000);
        $cheeseListing1->setDescription('cheese');

        $cheeseListing2 = new CheeseListing('cheese2');
        $cheeseListing2->setOwner($user);
        $cheeseListing2->setPrice(1000);
        $cheeseListing2->setDescription('cheese');

        $cheeseListing3 = new CheeseListing('cheese3');
        $cheeseListing3->setOwner($user);
        $cheeseListing3->setPrice(1000);
        $cheeseListing3->setDescription('cheese');

        $em = $this->getEntityManager();
        $em->persist($cheeseListing1);
        $em->persist($cheeseListing2);
        $em->persist($cheeseListing3);
        $em->flush();

        $client->request('GET', '/api/cheeses');

        $this->assertJsonContains(['hydra:totalItems' => 3]);
    }
}
