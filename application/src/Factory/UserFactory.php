<?php

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\ModelFactory;

final class UserFactory extends ModelFactory
{
    const DEFAULT_PASSWORD = 'test';

    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email,
            'username' => self::faker()->userName,
            // hashed version of "test"
            // php bin/console security:encode-password --env=test
            'password' => '$argon2id$v=19$m=10,t=3,p=1$eyXPWiQFWUO901E78Bb3UQ$hyu9dFDz7fo2opQyCSoX/NfJDvEpzER/a+WbiAagqqw',
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->beforeInstantiate(function(User $user) {})
        ;
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
