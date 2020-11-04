<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\ApiPlatform\CheeseSearchFilter;
use App\DTO\CheeseListingOutput;
use App\Repository\CheeseListingRepository;
use App\Validator\IsValidForPublication;
use App\Validator\IsValidOwner;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *      output=CheeseListingOutput::CLASS,
 *      collectionOperations={
 *          "get",
 *          "post"={"security"="is_granted('ROLE_USER')"}
 *      },
 *      itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"cheese:read", "cheese:item:get"}},
 *          },
 *          "put"={
 *              "security"="is_granted('EDIT', object)",
 *              "security_message"="only the creator can edit a cheese listing"
 *          },
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *      },
 *      shortName="cheese",
 *      normalizationContext={"groups"={"cheese:read"}},
 *      denormalizationContext={"groups"={"cheese:write"}},
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
 * @ApiFilter(CheeseSearchFilter::class)
 *
 * @ORM\Entity(repositoryClass=CheeseListingRepository::class)
 * @ORM\EntityListeners({"App\EventListener\Doctrine\CheeseListingSetOwnerListener"})
 *
 * @IsValidForPublication()
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
     * @Groups({"cheese:read", "cheese:write", "user:read", "user:write"})
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
     * @Groups({"cheese:read"})
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * The price of this delicious cheese, in cents.
     *
     * @Groups({"cheese:read", "cheese:write", "user:read", "user:write"})
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
     * @Groups({"cheese:write"})
     * @ORM\Column(type="boolean")
     */
    private $isPublished;

    /**
     * @Groups({"cheese:read", "cheese:write"})
     * @IsValidOwner()
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
     * @Groups({"cheese:write", "user:write"})
     * @SerializedName("description")
     */
    public function setTextDescription(?string $textDescription): self
    {
        $this->description = nl2br($textDescription);

        return $this;
    }

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
