<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Test\CustomApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserResourceTest extends CustomApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testCreateUser()
    {
        $client = self::createClient();

        $client->request('POST', '/api/users', [
            'json' => [
                'email' => 'cheeseplease@example.com',
                'username' => 'cheeseplease',
                'password' => 'brie',
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->login($client, 'cheeseplease@example.com', 'brie');
    }

    public function testUpdateUser()
    {
        $client = self::createClient();

        $user = $this->createUser('cheeseislife@example.com', 'cantal');

        $this->login($client, 'cheeseislife@example.com', 'cantal');

        $client->request('PUT', '/api/users/' . $user->getId(), [
            'json' => [
                'username' => 'cheezeislife',
                'roles' => ['ROLE_ADMIN'], // must be ignored
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'username' => 'cheezeislife',
        ]);

        $em = $this->getEntityManager();
        $user = $em->getRepository(User::class)->find($user->getId());

        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testGetUser()
    {
        $client = self::createClient();

        $user = UserFactory::new()->create([
            'phoneNumber' => '555.123.4567',
            'username' => 'cheesehead',
        ]);
        $authenticatedUser = UserFactory::new()->create();

        $this->login($client, $authenticatedUser->getEmail(), UserFactory::DEFAULT_PASSWORD);

        $client->request('GET', '/api/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'username' => $user->getUsername(),
            'isMe' => false,
            'isMvp' => true,
        ]);

        $data = $client->getResponse()->toArray();
        $this->assertArrayNotHasKey('phoneNumber', $data);

        // refresh the user & elevate
        $user->refresh();
        $user->setRoles(['ROLE_ADMIN']);
        $user->save();

        $this->login($client, $user->getEmail(), UserFactory::DEFAULT_PASSWORD);
        $client->request('GET', '/api/users/'.$user->getId());
        $this->assertJsonContains([
            'phoneNumber' => '555.123.4567',
            'isMe' => true,
        ]);
    }
}
