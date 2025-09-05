<?php

namespace App\Entity;

use App\Repository\CommentsRepository;
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
use App\Controller\CommentsController;

#[ORM\Entity(repositoryClass: CommentsRepository::class)]
// Defines the route that adds an operation
#[ApiResource(
    uriTemplate: '/users/{user_id}/offers/{offer_id}/comments',
    operations: [new Post()],
    controller: CommentsController::class
)]

// Defines the route that gets an operation
#[ApiResource(
    uriTemplate: '/offers/{offer_id}/comments',
    uriVariables: [
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
    ],
    operations: [new GetCollection()]
)]

// Defines the route that gets all the operations
// #[ApiResource(
//     uriTemplate: '/users/{user_id}/offers/{offer_id}/comments',
//     uriVariables: [
//         'user_id' => new Link(fromClass: User::class, toProperty: 'sender'),
//         'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
//     ],
//     operations: [new GetCollection()]
// )]

// Defines the route that sets an operation
// #[ApiResource(
//     uriTemplate: '/users/{user_id}/offers/{offer_id}/comments/{comment_id}',
//     uriVariables: [
//         'user_id' => new Link(fromClass: User::class, toProperty: 'sender'),
//         'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
//         'comment_id' => new Link(fromClass: Comments::class),
//     ],
//     operations: [new Patch()]
// )]

// Defines the route that deletes an operation
#[ApiResource(
    uriTemplate: '/users/{user_id}/offers/{offer_id}/comments/{comment_id}',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toProperty: 'sender'),
        'comment_id' => new Link(fromClass: Comments::class),
        'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
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
class Comments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read', 'write'])]
    private ?string $content = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?User $sender = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?Offers $offer = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

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

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

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

}
