<?php

namespace App\Entity;

use App\Entity\Enums\ApplicationState;
use App\Repository\ApplicationsRepository;
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
// use App\Controller\ApplicationsController;

#[ORM\Entity(repositoryClass: ApplicationsRepository::class)]

// Defines the route that adds a application
#[ApiResource(
    uriTemplate: '/offers/{offer_id}/applications',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
    ],
    operations: [new Post(read: false)],
    // controller: applicationsController::class
)]

#[ApiResource(
    uriTemplate: '/offers/{offer_id}/applications',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
    ],
    operations: [new GetCollection()],
)]

// Defines the route that gets an operation
#[ApiResource(
    uriTemplate: '/offers/{offer_id}/applications/{application_id}',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
        'application_id' => new Link(fromClass: Applications::class),
    ],
    operations: [new Get()]
)]

// Defines the route that sets an operation
#[ApiResource(
    uriTemplate: '/offers/{offer_id}/applications/{application_id}',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
        'application_id' => new Link(fromClass: Applications::class),
    ],
    operations: [new Patch()]
)]

// Defines the route that deletes an operation
#[ApiResource(
    uriTemplate: '/offers/{offer_id}/applications/{application_id}',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
        'application_id' => new Link(fromClass: Applications::class),
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

class Applications
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?int $id = null;

    // #[ORM\ManyToOne(inversedBy: 'applications')]
    // #[ORM\JoinColumn(nullable: false)]
    // private ?offer $offer_candidate = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[Groups(['read', 'write'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Offers $offer = null;

    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Documents>
     */
    #[ORM\ManyToMany(targetEntity: Documents::class, inversedBy: 'applications')]
    #[Groups(['read', 'write'])]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $documents;

    #[ORM\Column(enumType: ApplicationState::class, options: ["default" => ApplicationState::Evaluating])]
    private ?ApplicationState $state = null;

    // /**
    //  * @var Collection<int, Documents>
    //  */
    // #[ORM\OneToMany(targetEntity: Documents::class, mappedBy: 'applications')]
    // private Collection $document;

    public function __construct()
    {
        // $this->document = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->documents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // public function getofferCandidate(): ?offer
    // {
    //     return $this->offer_candidate;
    // }

    // public function setofferCandidate(?offer $offer_candidate): static
    // {
    //     $this->offer_candidate = $offer_candidate;

    //     return $this;
    // }
    
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

    // /**
    //  * @return Collection<int, Documents>
    //  */
    // public function getDocument(): Collection
    // {
    //     return $this->document;
    // }

    // public function addDocument(Documents $document): static
    // {
    //     if (!$this->documents->contains($document)) {
    //         $this->documents->add($document);
    //         $document->setApplications($this);
    //     }

    //     return $this;
    // }

    // public function removeDocument(Documents $document): static
    // {
    //     if ($this->documents->removeElement($document)) {
    //         // set the owning side to null (unless already changed)
    //         if ($document->getApplications() === $this) {
    //             $document->setApplications(null);
    //         }
    //     }

    //     return $this;
    // }

    // /**
    //  * @return Collection<int, Documents>
    //  */
    // public function getDocuments(): Collection
    // {
    //     return $this->documents;
    // }

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
            // $documents->addApplication($this);
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
