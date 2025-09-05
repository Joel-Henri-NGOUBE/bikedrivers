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
    // uriVariables: [
    //     'document_id' => new Link(fromClass: documents::class, toProperty: 'document')
    // ],
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

// #[ApiResource(
//     uriTemplate: '/users/{user_id}/documents/{document_id}',
//     uriVariables: [
        // 'user_id' => new Link(fromClass: User::class, toProperty: 'user'),
//         'user_id' => new Link(fromClass: User::class, toClass: documents::class, fromProperty: 'documents'),
//         'document_id' => new Link(fromClass: documents::class),
//     ],
//     operations: [new Get()]
// )]

#[ApiResource(
    uriTemplate: '/users/{user_id}/documents/{document_id}',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toProperty: 'user'),
        'document_id' => new Link(fromClass: Documents::class),
    ],
    operations: [new Patch()],
    controller: DocumentsControllers\PatchDocumentController::class
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

    #[ORM\Column(enumType: Enums\State::class)]
    private ?State $state = null;

    #[Vich\UploadableField(mapping: 'documents', fileNameProperty: 'path')]
    private ?File $documentFile = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $AddedAt = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, Applications>
     */
    #[ORM\ManyToMany(targetEntity: Applications::class, mappedBy: 'documents')]
    private Collection $applications;

    /**
     * @var Collection<int, RequiredDocuments>
     */
    #[ORM\ManyToMany(targetEntity: RequiredDocuments::class, inversedBy: 'documents')]
    private Collection $requiredDocuments;

    // #[ORM\ManyToOne(inversedBy: 'document')]
    // #[ORM\JoinColumn(nullable: false)]
    // private ?Applications $applications = null;

    public function __construct()
    {
        $this->AddedAt = new \DateTimeImmutable();
        $this->applications = new ArrayCollection();
        $this->requiredDocuments = new ArrayCollection();
        $this->appli = new ArrayCollection();
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

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(State $state): static
    {
        $this->state = $state;

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
            // Changer pour mettre updatedAt
            $this->AddedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->AddedAt;
    }

    public function setAddedAt(): static
    {
        $this->AddedAt = new \DateTimeImmutable();

        return $this;
    }

    // public function getApplications(): ?Applications
    // {
    //     return $this->applications;
    // }

    // public function setApplications(?Applications $applications): static
    // {
    //     $this->applications = $applications;

    //     return $this;
    // }

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

    /**
     * @return Collection<int, RequiredDocuments>
     */
    public function getRequiredDocuments(): Collection
    {
        return $this->requiredDocuments;
    }

    public function addRequiredDocument(RequiredDocuments $requiredDocument): static
    {
        if (!$this->requiredDocuments->contains($requiredDocument)) {
            $this->requiredDocuments->add($requiredDocument);
        }

        return $this;
    }

    public function removeRequiredDocument(RequiredDocuments $requiredDocument): static
    {
        $this->requiredDocuments->removeElement($requiredDocument);

        return $this;
    }

}
