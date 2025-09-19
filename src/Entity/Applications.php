<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Applications\AppliersController;
use App\Controller\Applications\HasAppliedController;
use App\Entity\Enums\ApplicationState;
use App\Repository\ApplicationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ApplicationsRepository::class)]

// Defines the route that adds a application
#[ApiResource(
    uriTemplate: '/offers/{offer_id}/applications',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
    ],
    operations: [new Post(read: false)],
    security: "is_granted('ROLE_ADMIN')"
    // controller: applicationsController::class
)]

#[ApiResource(
    uriTemplate: '/offers/{offer_id}/applications',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
    ],
    security: "is_granted('ROLE_ADMIN')",
    operations: [new GetCollection()]
)]

#[ApiResource(
    uriTemplate: '/offers/{offer_id}/applications/appliers',
    operations: [new GetCollection()],
    security: "is_granted('ROLE_ADMIN')",
    controller: AppliersController::class,
)]

#[ApiResource(
    uriTemplate: '/offers/{offer_id}/applications/users/{user_id}/hasApplied',
    operations: [new Get()],
    controller: HasAppliedController::class
)]

// Defines the route that sets an operation
#[ApiResource(
    uriTemplate: '/offers/{offer_id}/applications/{application_id}',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
        'application_id' => new Link(fromClass: Applications::class),
    ],
    security: "is_granted('ROLE_ADMIN')",
    operations: [new Patch()]
)]

// Defines the route that deletes an operation
#[ApiResource(
    uriTemplate: '/offers/{offer_id}/applications/{application_id}',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
        'application_id' => new Link(fromClass: Applications::class),
    ],
    security: "is_granted('ROLE_ADMIN')",
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

class Applications
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[Groups(['read', 'write'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Offers $offer = null;

    #[Groups(['read', 'write'])]
    #[ORM\Column(nullable: false)]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Documents>
     */
    #[ORM\ManyToMany(targetEntity: Documents::class, inversedBy: 'applications', orphanRemoval: true)]
    #[Groups(['read', 'write'])]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $documents;

    #[ORM\Column(enumType: ApplicationState::class, nullable: false, options: [
        'default' => ApplicationState::Evaluating,
    ])]
    private ?ApplicationState $state = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->documents = new ArrayCollection();
        $this->state = ApplicationState::Evaluating;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOffer(): ?Offers
    {
        return $this->offer;
    }

    public function setOffer(?Offers $offer): static
    {
        $this->offer = $offer;

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

    /**
     * @return Collection<int, Documents>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Documents $document): static
    {
        if (! $this->documents->contains($document)) {
            $this->documents->add($document);
        }

        return $this;
    }

    public function removeDocument(Documents $document): static
    {
        $this->documents->removeElement($document);

        return $this;
    }

    public function getState(): ?ApplicationState
    {
        return $this->state;
    }

    public function setState(ApplicationState $state): static
    {
        $this->state = $state;

        return $this;
    }
}
