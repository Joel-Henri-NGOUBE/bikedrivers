<?php

namespace App\Entity;

use App\Repository\MessagesRepository;
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
use App\Controller\Messages as MessagesControllers;

#[ORM\Entity(repositoryClass: MessagesRepository::class)]

// Defines the route that adds an operation
#[ApiResource(
    uriTemplate: '/offers/{offer_id}/sender/{sender_id}/recipient/{recipient_id}/messages',
    operations: [new Post()],
    controller: MessagesControllers\PostMessagesController::class
)]

// Defines the route that gets an operation
#[ApiResource(
    // L'utilisateur envoyant des messages et n'étant pas initiateur de l'offre
    // C'est à moi de créer le controleur et les autres controleurs
    uriTemplate: '/offers/{offer_id}/users/{user_id}/messages',
    // uriVariables: [
        // 'sender_id' => new Link(fromClass: User::class, toClass: Messages::class, toProperty: 'user_sender'),
        // // 'sender_id' => new Link(fromClass: User::class, toProperty: 'user'),
        // 'recipient_id' => new Link(fromClass: User::class, toClass: Messages::class, toProperty: 'user_recipient'),
        // 'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
        // // 'message_id' => new Link(fromClass: Messages::class),
    // ],
    operations: [new GetCollection()],
    controller: MessagesControllers\GetMessagesController::class
)]

#[ApiResource(
    uriTemplate: '/offers/{offer_id}/messages/users',
    // uriVariables: [
    //     // 'user_id' => new Link(fromClass: User::class, toClass: Vehicles::class, fromProperty: 'vehicles'),
    //     'vehicle_id' => new Link(fromClass: Vehicles::class, toProperty: 'vehicle'),
    //     'offer_id' => new Link(fromClass: Offers::class),
    // ],
    operations: [new GetCollection()],
    controller: MessagesControllers\GetUsersController::class
    // controller: GetAnOfferController::class
)]

#[ApiResource(
    uriTemplate: '/offers/{offer_id}/users/{user_id}/messages/{message_id}',
    // uriVariables: [
    //     'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
    //     'sender_id' => new Link(fromClass: User::class, toProperty: 'user_sender'),
    //     'recipient_id' => new Link(fromClass: User::class, toProperty: 'user_recipient'),
    //     // 'sender_id' => new Link(fromClass: User::class, toClass: Vehicles::class, fromProperty: 'vehicles'),
    //     'message_id' => new Link(fromClass: Messages::class),
    // ],
    operations: [new Patch()],
    controller: MessagesControllers\PatchMessageController::class
)]

#[ApiResource(
    uriTemplate: '/offers/{offer_id}/users/{user_id}/messages/{message_id}',
    // uriVariables: [
    //     'offer_id' => new Link(fromClass: Offers::class, toProperty: 'offer'),
    //     'sender_id' => new Link(fromClass: User::class, toProperty: 'user_sender'),
    //     'recipient_id' => new Link(fromClass: User::class, toProperty: 'user_recipient'),
    //     // 'sender_id' => new Link(fromClass: User::class, toClass: Vehicles::class, fromProperty: 'vehicles'),
    //     'message_id' => new Link(fromClass: Messages::class),
    // ],
    operations: [new Delete()],
    controller: MessagesControllers\DeleteMessageController::class
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


class Messages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private ?string $content = null;

    #[ORM\Column(nullable: false)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'messagesSent')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_sender = null;

    #[ORM\ManyToOne(inversedBy: 'messagesReceived')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_recipient = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
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

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUserSender(): ?User
    {
        return $this->user_sender;
    }

    public function setUserSender(?User $user_sender): static
    {
        $this->user_sender = $user_sender;

        return $this;
    }

    public function getUserRecipient(): ?User
    {
        return $this->user_recipient;
    }

    public function setUserRecipient(?User $user_recipient): static
    {
        $this->user_recipient = $user_recipient;

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
