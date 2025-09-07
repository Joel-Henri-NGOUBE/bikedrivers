<?php

namespace App\Entity;

use App\Entity\Enums\State;
use App\Repository\MatchDocumentsRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\MatchDocumentsController;

#[ORM\Entity(repositoryClass: MatchDocumentsRepository::class)]

// Defines the route that adds an operation
#[ApiResource(
    uriTemplate: '/required_documents/{required_document_id}/documents/{document_id}/match_documents',
    operations: [new Post()],
    controller: MatchDocumentsController::class
)]

#[ApiResource(
    uriTemplate: '/required_documents/{required_document_id}/documents/{document_id}/match_documents/{match_document_id}',
    uriVariables: [
        'required_document_id' => new Link(fromClass: RequiredDocuments::class, toProperty: 'requiredDocument'),
        'match_document_id' => new Link(fromClass: MatchDocuments::class),
        'document_id' => new Link(fromClass: Documents::class, toProperty: 'document'),
    ],
    operations: [new Get()]
)]

// Defines the route that sets an operation
#[ApiResource(
    uriTemplate: '/required_documents/{required_document_id}/documents/{document_id}/match_documents/{match_document_id}',
    uriVariables: [
        'required_document_id' => new Link(fromClass: RequiredDocuments::class, toProperty: 'requiredDocument'),
        'match_document_id' => new Link(fromClass: MatchDocuments::class),
        'document_id' => new Link(fromClass: Documents::class, toProperty: 'document'),
    ],
    operations: [new Patch()]
)]

// Defines the route that deletes an operation
#[ApiResource(
    uriTemplate: '/required_documents/{required_document_id}/documents/{document_id}/match_documents/{match_document_id}',
    uriVariables: [
        'required_document_id' => new Link(fromClass: RequiredDocuments::class, toProperty: 'requiredDocument'),
        'match_document_id' => new Link(fromClass: MatchDocuments::class),
        'document_id' => new Link(fromClass: Documents::class, toProperty: 'document'),
    ],
    operations: [new Delete()]
)]

class MatchDocuments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: State::class, options: ["default" => State::Unevaluated], nullable: false)]
    private ?State $state = null;

    #[ORM\ManyToOne(inversedBy: 'matchDocuments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Documents $document = null;

    #[ORM\ManyToOne(inversedBy: 'matchDocuments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RequiredDocuments $requiredDocument = null;

    public function __construct()
    {
        $this->state = State::Unevaluated;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDocument(): ?Documents
    {
        return $this->document;
    }

    public function setDocument(?Documents $document): static
    {
        $this->document = $document;

        return $this;
    }

    public function getRequiredDocument(): ?RequiredDocuments
    {
        return $this->requiredDocument;
    }

    public function setRequiredDocument(?RequiredDocuments $requiredDocument): static
    {
        $this->requiredDocument = $requiredDocument;

        return $this;
    }
}
