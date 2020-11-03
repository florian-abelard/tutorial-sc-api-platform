<?php

namespace App\Factory;

use App\Entity\CheeseNotification;
use Zenstruck\Foundry\ModelFactory;

final class CheeseNotificationFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'cheeseListing' => CheeseListingFactory::new(),
            'notificationText' => self::faker()->realText(50),
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->beforeInstantiate(function(CheeseNotification $cheeseNotification) {})
        ;
    }

    protected static function getClass(): string
    {
        return CheeseNotification::class;
    }
}
