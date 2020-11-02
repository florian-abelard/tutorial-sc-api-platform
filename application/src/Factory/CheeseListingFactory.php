<?php

namespace App\Factory;

use App\Entity\CheeseListing;
use Zenstruck\Foundry\ModelFactory;

final class CheeseListingFactory extends ModelFactory
{
    public function published(): self
    {
        return $this->addState(['isPublished' => true]);
    }

    public function withLongDescription(): self
    {
        return $this->addState([
            'description' => self::faker()->paragraphs(3, true),
        ]);
    }

    protected function getDefaults(): array
    {
        return [
            'title' => 'Block of cheddar',
            'description' => 'What can I say? A raw cube of cheese power',
            'price' => 1500,
            // allow this factory to create an owner
            'owner' => UserFactory::new(),
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->beforeInstantiate(function(CheeseListing $cheeseListing) {})
        ;
    }

    protected static function getClass(): string
    {
        return CheeseListing::class;
    }
}
