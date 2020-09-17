<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\CheeseListingRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *      collectionOperations={
 *          "get",
 *          "post"={"security"="is_granted('ROLE_USER')"}
 *      },
 *      itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"cheese_listing:read", "cheese_listing:item:get"}},
 *          },
 *          "put"={"security"="is_granted('ROLE_USER')"},
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *      },
 *      shortName="cheeses",
 *      normalizationContext={"groups"={"cheese_listing:read"}},
 *      denormalizationContext={"groups"={"cheese_listing:write"}},
 *      attributes={
 *          "formats"={"jsonld", "json", "html", "jsonhal", "csv"={"text/csv"}}
 *      }
 * )
 * @ApiFilter(BooleanFilter::class, properties={"isPublished"})
 * @ApiFilter(
 *      SearchFilter::class,
 *      properties={
 *          "title": "ipartial",
 *          "description": "ipartial",
 *          "owner": "exact",
 *          "owner.username": "ipartial"
 *      }
 * )
 * @ApiFilter(RangeFilter::class, properties={"price"})
 * @ApiFilter(PropertyFilter::class)
 *
 * @ORM\Entity(repositoryClass=CheeseListingRepository::class)
 */
class CheeseListing
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"cheese_listing:read", "cheese_listing:write", "user:read", "user:write"})
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min=2,
     *      max=50,
     *      maxMessage="Describe your cheese in 50 chars or less"
     * )
     */
    private $title;

    /**
     * @Groups({"cheese_listing:read"})
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * The price of this delicious cheese, in cents.
     *
     * @Groups({"cheese_listing:read", "cheese_listing:write", "user:read", "user:write"})
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value=0)
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished;

    /**
     * @Groups({"cheese_listing:read", "cheese_listing:write"})
     *
     * @Assert\Valid()
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="cheeseListings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    public function __construct(string $title)
    {
        $this->title = $title;
        $this->createdAt = new \DateTimeImmutable();
        $this->isPublished = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @Groups({"cheese_listing:write", "user:write"})
     * @SerializedName("description")
     */
    public function setTextDescription(?string $textDescription): self
    {
        $this->description = nl2br($textDescription);

        return $this;
    }

    /**
     * @Groups({"cheese_listing:read"})
     */
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) < 45) {
            return $this->description;
        }

        return substr($this->description, 0, 40).'...';
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * How long ago in text that this cheese listing was added.
     *
     * @Groups({"cheese_listing:read"})
     */
    public function getCreatedAtAgo(): ?string
    {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
