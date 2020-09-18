<?php

namespace App\Tests\Functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UserResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

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

        $this->logIn($client, 'cheeseplease@example.com', 'brie');
    }

    public function testUpdateUser()
    {
        $client = self::createClient();

        $user = $this->createUser('cheeseislife@example.com', 'cantal');

        $this->logIn($client, 'cheeseislife@example.com', 'cantal');

        $client->request('PUT', '/api/users/' . $user->getId(), [
            'json' => [
                'username' => 'cheezeislife',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'username' => 'cheezeislife',
        ]);
    }
}
