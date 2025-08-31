<?php

namespace App\Entity;

use App\Repository\OffersRepository;
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
use App\Controller\OffersController;
use App\Controller\GetAnOfferController;
use App\Entity\Enums\Status;
use ApiPlatform\Metadata\ApiProperty;

#[ORM\Entity(repositoryClass: OffersRepository::class)]
// Defines the route that adds an operation
#[ApiResource(
    uriTemplate: '/users/{user_id}/vehicles/{vehicle_id}/offers',
    operations: [new Post()],
    controller: OffersController::class
)]

// Defines the route that gets an operation
#[ApiResource(
    uriTemplate: '/users/{user_id}/vehicles/{vehicle_id}/offers',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toProperty: 'user'),
        'vehicle_id' => new Link(fromClass: Vehicles::class, toProperty: 'vehicle'),
    ],
    operations: [new GetCollection()]
)]

// #[ApiResource(
//     uriTemplate: '/vehicles/{vehicle_id}/offers/{offer_id}',
//     uriVariables: [
//         // 'user_id' => new Link(fromClass: User::class, toClass: Vehicles::class, fromProperty: 'vehicles'),
//         'vehicle_id' => new Link(fromClass: Vehicles::class, toProperty: 'vehicle'),
//         'offer_id' => new Link(fromClass: Offers::class),
//     ],
//     operations: [new Get()],
//     // controller: GetAnOfferController::class
// )]

#[ApiResource(
    uriTemplate: '/users/{user_id}/vehicles/{vehicle_id}/offers/{offer_id}',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toClass: Vehicles::class, fromProperty: 'vehicles'),
        'vehicle_id' => new Link(fromClass: Vehicles::class, toProperty: 'vehicle'),
        'offer_id' => new Link(fromClass: Offers::class),
    ],
    operations: [new Patch()]
)]

#[ApiResource(
    uriTemplate: '/users/{user_id}/vehicles/{vehicle_id}/offers/{offer_id}',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toClass: Vehicles::class, fromProperty: 'vehicles'),
        'vehicle_id' => new Link(fromClass: Vehicles::class, toProperty: 'vehicle'),
        'offer_id' => new Link(fromClass: Offers::class),
    ],
    operations: [new Delete()]
)]

#[ApiResource(
    operations: [
        new GetCollection(write: false),
    ],
    uriTemplate: '/offers',
    security: "is_granted('PUBLIC_ACCESS')",
)]

#[ApiResource(
    operations: [new Get(
    uriTemplate: '/offers/{id}',
    security: "is_granted('PUBLIC_ACCESS')",
    write: false
)])]

#[ApiResource(
    operations: [
        new Post(write: false, read: false),
    ],
    uriTemplate: '/offers'
)]

#[ApiResource(
    operations: [
        new Patch(write: false, read: false),
        new Delete(write: false, read: false),
    ],
    uriTemplate: '/offers/{id}'
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
    // #[ApiProperty(identifier: true)]
    // #[ApiProperty(identifier: true)]
    private ?int $id = null;
    
    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    #[Groups(['read', 'write'])]
    private ?string $id_taker = null;

    // #[ORM\Column(length: 255)]
    // private ?string $title = null;
    
    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read', 'write'])]
    private ?string $description = null;
    
    #[ORM\Column(enumType: Status::class)]
    #[Groups(['read', 'write'])]
    private ?Status $status = null;
    
    #[ORM\Column(nullable: true)]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $boughtAt = null;
    
    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $createdAt = null;
    
    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $updatedAt = null;
    
    #[ORM\Column(nullable: true)]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $rentedFromAt = null;
    
    #[ORM\Column(nullable: true)]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $rentedToAt = null;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?Vehicles $vehicle = null;

    /**
     * @var Collection<int, Comments>
    */
    #[ORM\OneToMany(targetEntity: Comments::class, mappedBy: 'offer')]
    #[Groups(['read', 'write'])]
    private Collection $comments;

    // /**
    //  * @var Collection<int, Applications>
    // */
    // #[ORM\OneToMany(targetEntity: Applications::class, mappedBy: 'offer')]
    // #[Groups(['read', 'write'])]
    // private Collection $applications;

    // /**
    //  * @var Collection<int, Messages>
    // */
    // #[ORM\OneToMany(targetEntity: Messages::class, mappedBy: 'offer')]
    // #[Groups(['read', 'write'])]
    // private Collection $messages;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        // $this->applications = new ArrayCollection();
        // $this->messages = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
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

    // public function getTitle(): ?string
    // {
    //     return $this->title;
    // }

    // public function setTitle(string $title): static
    // {
    //     $this->title = $title;

    //     return $this;
    // }

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

    public function getBoughtAt(): ?\DateTimeImmutable
    {
        return $this->boughtAt;
    }

    public function setBoughtAt(): static
    {
        $this->boughtAt = new \DateTimeImmutable();

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

    public function getRentedFromAt(): ?\DateTimeImmutable
    {
        return $this->rentedFromAt;
    }

    public function setRentedFromAt(): static
    {
        $this->rentedFromAt = new \DateTimeImmutable();

        return $this;
    }

    public function getRentedToAt(): ?\DateTimeImmutable
    {
        return $this->rentedToAt;
    }

    public function setRentedToAt(): static
    {
        $this->rentedToAt = new \DateTimeImmutable();

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
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setOffer($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getOffer() === $this) {
                $comment->setOffer(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection<int, Applications>
    //  */
    // public function getApplications(): Collection
    // {
    //     return $this->applications;
    // }

    // public function addApplication(Applications $application): static
    // {
    //     if (!$this->applications->contains($application)) {
    //         $this->applications->add($application);
    //         $application->setOffer($this);
    //     }

    //     return $this;
    // }

    // public function removeApplication(Applications $application): static
    // {
    //     if ($this->applications->removeElement($application)) {
    //         // set the owning side to null (unless already changed)
    //         if ($application->getOffer() === $this) {
    //             $application->setOffer(null);
    //         }
    //     }

    //     return $this;
    // }

    // /**
    //  * @return Collection<int, Messages>
    //  */
    // public function getMessages(): Collection
    // {
    //     return $this->messages;
    // }

    // public function addMessage(Messages $message): static
    // {
    //     if (!$this->messages->contains($message)) {
    //         $this->messages->add($message);
    //         $message->setOffer($this);
    //     }

    //     return $this;
    // }

    // public function removeMessage(Messages $message): static
    // {
    //     if ($this->messages->removeElement($message)) {
    //         // set the owning side to null (unless already changed)
    //         if ($message->getOffer() === $this) {
    //             $message->setOffer(null);
    //         }
    //     }

    //     return $this;
    // }
    
}
