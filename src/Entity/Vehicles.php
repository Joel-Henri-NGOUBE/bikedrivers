<?php

namespace App\Entity;

use App\Repository\VehiclesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\VehiclesController;
use ApiPlatform\Metadata\ApiProperty;

#[ORM\Entity(repositoryClass: VehiclesRepository::class)]
#[ApiResource]
// Defines the route that adds a vehicle
#[ApiResource(
    uriTemplate: '/users/{user_id}/vehicles',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toProperty: 'user'),
    ],
    operations: [new Post()],
    controller: VehiclesController::class
)]

#[ApiResource(
    uriTemplate: '/users/{user_id}/vehicles',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toProperty: 'user'),
    ],
    operations: [new GetCollection()],
)]

// Defines the route that gets an operation
#[ApiResource(
    uriTemplate: '/users/{user_id}/vehicles/{vehicle_id}',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toProperty: 'user'),
        'vehicle_id' => new Link(fromClass: Vehicles::class),
    ],
    operations: [new Get()]
)]

// Defines the route that sets an operation
#[ApiResource(
    uriTemplate: '/users/{user_id}/vehicles/{vehicle_id}',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toProperty: 'user'),
        'vehicle_id' => new Link(fromClass: Vehicles::class),
    ],
    operations: [new Patch()]
)]

// Defines the route that deletes an operation
#[ApiResource(
    uriTemplate: '/users/{user_id}/vehicles/{vehicle_id}',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toProperty: 'user'),
        'vehicle_id' => new Link(fromClass: Vehicles::class),
    ],
    operations: [new Delete()]
)]

// Defining serializer options
#[ApiResource(
    normalizationContext: [
        'groups' => ['read'],
    ],
    denormalizationContext: [
        'groups' => ['write'],
    ],
)]

class Vehicles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read', 'write'])]
    // #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['read', 'write'])]
    private ?string $type = null;

    #[ORM\Column(length: 50)]
    #[Groups(['read', 'write'])]
    private ?string $model = null;

    #[ORM\Column(length: 50)]
    #[Groups(['read', 'write'])]
    private ?string $brand = null;

    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $purchasedAt = null;

    /**
     * @var Collection<int, Offers>
     */
    #[ORM\OneToMany(targetEntity: Offers::class, mappedBy: 'vehicle')]
    #[Groups(['read', 'write'])]
    private Collection $offers;

    /**
     * @var Collection<int, Pictures>
     */
    #[ORM\OneToMany(targetEntity: Pictures::class, mappedBy: 'vehicle')]
    #[Groups(['read', 'write'])]
    private Collection $pictures;

    #[ORM\ManyToOne(inversedBy: 'vehicles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?User $user = null;

    public function __construct()
    {
        $this->offers = new ArrayCollection();
        // $this->pictures = new ArrayCollection();
        $this->addedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(): static
    {
        $this->addedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getPurchasedAt(): ?\DateTimeImmutable
    {
        return $this->purchasedAt;
    }

    public function setPurchasedAt(\DateTimeImmutable $purchasedAt): static
    {
        $this->purchasedAt = $purchasedAt;

        return $this;
    }

    /**
     * @return Collection<int, Offers>
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offers $offer): static
    {
        if (!$this->offers->contains($offer)) {
            $this->offers->add($offer);
            $offer->setVehicle($this);
        }

        return $this;
    }

    public function removeOffer(Offers $offer): static
    {
        if ($this->offers->removeElement($offer)) {
            // set the owning side to null (unless already changed)
            if ($offer->getVehicle() === $this) {
                $offer->setVehicle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pictures>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Pictures $picture): static
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setVehicle($this);
        }

        return $this;
    }

    public function removePicture(Pictures $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getVehicle() === $this) {
                $picture->setVehicle(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
