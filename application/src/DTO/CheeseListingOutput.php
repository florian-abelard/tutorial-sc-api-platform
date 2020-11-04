<?php

namespace App\DTO;

use App\Entity\User;
use Carbon\Carbon;
use Symfony\Component\Serializer\Annotation\Groups;

class CheeseListingOutput
{
    /**
     * @Groups({"cheese:read"})
     */
    public string $title;

    /**
     * @Groups({"cheese:read"})
     */
    public string $description;

    /**
     * @Groups({"cheese:read"})
     */
    public int $price;

    /**
     * @Groups({"cheese:read"})
     */
    public User $owner;

    public \DateTimeInterface $createdAt;

    /**
     * @Groups({"cheese:read"})
     */
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) < 45) {
            return $this->description;
        }

        return substr($this->description, 0, 40).'...';
    }

    /**
     * How long ago in text that this cheese listing was added.
     *
     * @Groups({"cheese:read"})
     */
    public function getCreatedAtAgo(): ?string
    {
        return Carbon::instance($this->createdAt)->diffForHumans();
    }
}
