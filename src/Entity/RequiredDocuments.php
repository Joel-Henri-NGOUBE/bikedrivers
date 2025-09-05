<?php

namespace App\Entity;

use App\Repository\RequiredDocumentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\RequiredDocumentsController;

#[ORM\Entity(repositoryClass: RequiredDocumentsRepository::class)]

#[ApiResource(
    uriTemplate: '/offers/{offer_id}/required_documents',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
    ],
    operations: [new Post(read: false)],
)]

#[ApiResource(
    uriTemplate: '/offers/{offer_id}/required_documents',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
    ],
    operations: [new GetCollection()],
)]

// Defines the route that gets an operation
#[ApiResource(
    uriTemplate: '/offers/{offer_id}/required_documents/{required_document_id}',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
        'required_document_id' => new Link(fromClass: RequiredDocuments::class),
    ],
    operations: [new Get()]
)]

// Defines the route that sets an operation
#[ApiResource(
    uriTemplate: '/offers/{offer_id}/required_documents/{required_document_id}',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
        'required_document_id' => new Link(fromClass: RequiredDocuments::class),
    ],
    operations: [new Patch()],
)]

#[ApiResource(
    uriTemplate: '/offers/{offer_id}/required_documents/{required_document_id}/documents/{document_id}',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
        'required_document_id' => new Link(fromClass: RequiredDocuments::class),
    ],
    operations: [new Patch()],
    controller: RequiredDocumentsController::class
)]

// Defines the route that deletes an operation
#[ApiResource(
    uriTemplate: '/offers/{offer_id}/required_documents/{required_document_id}',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
        'required_document_id' => new Link(fromClass: RequiredDocuments::class),
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

class RequiredDocuments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read', 'write'])]
    private ?string $informations = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'requiredDocuments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?Offers $offer = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Groups(['read', 'write'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Documents>
     */
    #[ORM\ManyToMany(targetEntity: Documents::class, mappedBy: 'requiredDocuments')]
    private Collection $documents;

    public function __construct()
    {
        $this->setCreatedAt();
        $this->setUpdatedAt();
        $this->documents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInformations(): ?string
    {
        return $this->informations;
    }

    public function setInformations(?string $informations): static
    {
        $this->informations = $informations;

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

    public function getOffer(): ?Offers
    {
        return $this->offer;
    }

    public function setOffer(?Offers $offer): static
    {
        $this->offer = $offer;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->addRequiredDocument($this);
        }

        return $this;
    }

    public function removeDocument(Documents $document): static
    {
        if ($this->documents->removeElement($document)) {
            $document->removeRequiredDocument($this);
        }

        return $this;
    }
}
