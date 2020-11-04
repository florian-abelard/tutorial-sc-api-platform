<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\CheeseListingOutput;
use App\Entity\CheeseListing;

class CheeseListingOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param CheeseListing $object
     */
    public function transform($object, string $to, array $context = [])
    {
        $output = new CheeseListingOutput();

        $output->title = $object->getTitle();
        $output->description = $object->getDescription();
        $output->price = $object->getPrice();
        $output->createdAt = $object->getCreatedAt();
        $output->owner = $object->getOwner();

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $data instanceof CheeseListing && $to === CheeseListingOutput::class;
    }
}
