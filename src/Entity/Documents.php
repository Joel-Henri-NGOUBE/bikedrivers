<?php

namespace App\Entity;

use App\Repository\DocumentsRepository;
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
use App\Controller\DocumentsController;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Entity\Enums\State;
use App\Controller\Documents as DocumentsControllers;

#[ORM\Entity(repositoryClass: DocumentsRepository::class)]

#[Vich\Uploadable]
#[ApiResource(
    uriTemplate: 'users/{user_id}/documents',
    operations: [new Post()],
    controller: DocumentsControllers\PostDocumentsController::class
)]

// Defines the route that gets an operation
#[ApiResource(
    uriTemplate: '/users/{user_id}/documents',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toProperty: 'user'),
    ],
    operations: [new GetCollection()]
)]

#[ApiResource(
    uriTemplate: 'applications/{application_id}/documents/elements',
    operations: [new GetCollection()],
    controller: DocumentsControllers\DocumentsElementsController::class
)]

#[ApiResource(
    uriTemplate: 'offers/{offer_id}/users/{user_id}/documents/elements',
    operations: [new GetCollection()],
    controller: DocumentsControllers\TransferedDocumentsElementsController::class
)]

#[ApiResource(
    uriTemplate: '/users/{user_id}/documents/{document_id}',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toProperty: 'user'),
        'document_id' => new Link(fromClass: Documents::class),
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

class Documents
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $path = null;

    #[Vich\UploadableField(mapping: 'documents', fileNameProperty: 'path')]
    private ?File $documentFile = null;

    #[ORM\Column(nullable: false)]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, Applications>
     */
    #[ORM\ManyToMany(targetEntity: Applications::class, mappedBy: 'documents')]
    private Collection $applications;

    #[ORM\Column(nullable: false)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, MatchDocuments>
     */
    #[ORM\OneToMany(targetEntity: MatchDocuments::class, mappedBy: 'document', orphanRemoval: true)]
    private Collection $matchDocuments;

    public function __construct()
    {
        $this->addedAt = new \DateTimeImmutable();
        $this->applications = new ArrayCollection();
        $this->matchDocuments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getDocumentFile(): ?File
    {
        return $this->documentFile;
    }

    public function setDocumentFile(File $file): static
    {
        $this->documentFile = $file;

        if($file){
            $this->setUpdatedAt();
        }

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->addDocument($this);
        }

        return $this;
    }

    public function removeApplication(Applications $application): static
    {
        if ($this->applications->removeElement($application)) {
            $application->removeDocument($this);
        }

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

    /**
     * @return Collection<int, MatchDocuments>
     */
    public function getMatchDocuments(): Collection
    {
        return $this->matchDocuments;
    }

    public function addMatchDocument(MatchDocuments $matchDocument): static
    {
        if (!$this->matchDocuments->contains($matchDocument)) {
            $this->matchDocuments->add($matchDocument);
            $matchDocument->setDocument($this);
        }

        return $this;
    }

    public function removeMatchDocument(MatchDocuments $matchDocument): static
    {
        if ($this->matchDocuments->removeElement($matchDocument)) {
            // set the owning side to null (unless already changed)
            if ($matchDocument->getDocument() === $this) {
                $matchDocument->setDocument(null);
            }
        }

        return $this;
    }

}
