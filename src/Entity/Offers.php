<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Offers as OffersControllers;
use App\Controller\OffersController;
use App\Entity\Enums\Service;
use App\Entity\Enums\Status;
use App\Repository\OffersRepository;
use App\State\DenyNotOwnerActionsOnCollectionProvider;
use App\State\DenyNotOwnerActionsOnItemProvider;
use App\State\DenyNotOwnerActionsProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OffersRepository::class)]
// Defines the route that adds an offer
#[ApiResource(
    uriTemplate: '/users/{user_id}/vehicles/{vehicle_id}/offers',
    operations: [new Post(read: false)],
    security: "is_granted('ROLE_ADMIN')",
    // processor: DenyNotOwnerActionsProcessor::class
    // controller: OffersController::class
)]

// Defines the route that gets all users' offers
#[ApiResource(
    uriTemplate: '/users/{user_id}/vehicles/{vehicle_id}/offers',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toProperty: 'user'),
        'vehicle_id' => new Link(fromClass: Vehicles::class, toProperty: 'vehicle'),
    ],
    operations: [new GetCollection()],
    provider: DenyNotOwnerActionsOnCollectionProvider::class,
    security: "is_granted('ROLE_ADMIN')"
)]

// Defines the routes that allow the user to acces his offers elements
#[ApiResource(
    uriTemplate: 'users/{user_id}/offers/elements',
    operations: [new GetCollection()],
    controller: OffersControllers\SelfOffersElementsController::class
)]

// Defines the routes that allow the user to acces the offers he applied to
#[ApiResource(
    uriTemplate: 'users/{user_id}/offers/applied',
    operations: [new GetCollection()],
    controller: OffersControllers\AppliedOffersController::class
)]

// Defines the routes that allow the user to modify an offer
#[ApiResource(
    uriTemplate: '/users/{user_id}/vehicles/{vehicle_id}/offers/{offer_id}',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toClass: Vehicles::class, fromProperty: 'vehicles'),
        'vehicle_id' => new Link(fromClass: Vehicles::class, toProperty: 'vehicle'),
        'offer_id' => new Link(fromClass: Offers::class),
    ],
    operations: [new Patch()],
    provider: DenyNotOwnerActionsOnItemProvider::class,
    security: "is_granted('ROLE_ADMIN')"
)]

#[ApiResource(
    operations: [
        new GetCollection(write: false),
    ],
    uriTemplate: '/offers',
    security: "is_granted('PUBLIC_ACCESS')",
)]

// #[ApiResource(
//     operations: [
//         new GetCollection(write: false),
//     ],
//     uriTemplate: '/offers/{id}',
//     security: "is_granted('PUBLIC_ACCESS')",
// )]

#[ApiResource(
    operations: [new Get(
        uriTemplate: '/offers/{id}',
        security: "is_granted('PUBLIC_ACCESS')",
        write: false
    )]
)]

#[ApiResource(
    operations: [
        new Post(write: false, read: false),
    ],
    uriTemplate: '/offers',
    security: "is_granted('ROLE_ADMIN')"
)]

#[ApiResource(
    operations: [
        new Patch(write: false, read: false),
        new Delete(),
    ],
    uriTemplate: '/offers/{id}',
    security: "is_granted('ROLE_ADMIN')"
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

class Offers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    #[Groups(['read', 'write'])]
    #[Assert\Type('integer')]
    private ?string $id_taker = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Groups(['read', 'write'])]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private ?string $description = null;

    #[ORM\Column(enumType: Status::class, nullable: true, options: [
        'default' => Status::Available,
    ])]
    #[Assert\NotBlank]
    #[Groups(['read', 'write'])]
    private ?Status $status = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $startsAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $endsAt = null;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?Vehicles $vehicle = null;

    /**
     * @var Collection<int, Applications>
     */
    #[ORM\OneToMany(targetEntity: Applications::class, mappedBy: 'offer', orphanRemoval: true)]
    #[Groups(['read', 'write'])]
    private Collection $applications;

    /**
     * @var Collection<int, RequiredDocuments>
     */
    #[ORM\OneToMany(targetEntity: RequiredDocuments::class, mappedBy: 'offer', orphanRemoval: true)]
    private Collection $requiredDocuments;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private ?string $title = null;

    #[ORM\Column(nullable: false)]
    #[Assert\Type('float')]
    #[Assert\Positive]
    #[Assert\NotBlank]
    private ?float $price = null;

    #[ORM\Column(enumType: Service::class, nullable: false, options: [
        'default' => Service::Location,
    ])]
    #[Assert\NotBlank]
    private ?Service $service = null;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->status = Status::Available;
        $this->service = Service::Location;
        $this->requiredDocuments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdTaker(): ?string
    {
        return $this->id_taker;
    }

    public function setIdTaker(?string $id_taker): static
    {
        $this->id_taker = $id_taker;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): static
    {
        $this->createdAt = new \DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): static
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getStartsAt(): ?\DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function setStartsAt(\DateTimeImmutable $startsAt): static
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getEndsAt(): ?\DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(\DateTimeImmutable $endsAt): static
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    public function getVehicle(): ?Vehicles
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicles $vehicle): static
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * @return Collection<int, Applications>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Applications $application): static
    {
        if (! $this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setOffer($this);
        }

        return $this;
    }

    public function removeApplication(Applications $application): static
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getOffer() === $this) {
                $application->setOffer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RequiredDocuments>
     */
    public function getRequiredDocuments(): Collection
    {
        return $this->requiredDocuments;
    }

    public function addRequiredDocument(RequiredDocuments $requiredDocument): static
    {
        if (! $this->requiredDocuments->contains($requiredDocument)) {
            $this->requiredDocuments->add($requiredDocument);
            $requiredDocument->setOffer($this);
        }

        return $this;
    }

    public function removeRequiredDocument(RequiredDocuments $requiredDocument): static
    {
        if ($this->requiredDocuments->removeElement($requiredDocument)) {
            // set the owning side to null (unless already changed)
            if ($requiredDocument->getOffer() === $this) {
                $requiredDocument->setOffer(null);
            }
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(Service $service): static
    {
        $this->service = $service;

        return $this;
    }
}
