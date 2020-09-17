<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class CustomApiTestCase extends ApiTestCase
{
    protected function createUser(string $email, string $password): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setUsername(substr($email, 0, strpos($email, '@')));
        $user->setPassword($password);

        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function login(Client $client, string $email, string $password): string
    {
        $response = $client->request('POST', '/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        return $response->getHeaders()['location'][0];
    }
}
