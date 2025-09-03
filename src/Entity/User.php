<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_MAIL', fields: ['mail'])]
// Defining serializer options
#[ApiResource(
    normalizationContext: [
        'groups' => ['read'],
    ],
    denormalizationContext: [
        'groups' => ['write'],
    ],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read', 'write'])]
    // #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['read', 'write'])]
    private ?string $mail = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['read'])]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    #[Groups(['read', 'write'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    #[Groups(['read', 'write'])]
    private ?string $lastname = null;

    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Comments>
     */
    #[ORM\OneToMany(targetEntity: Comments::class, mappedBy: 'RgetUserRecipient')]
    #[Groups(['read', 'write'])]
    private Collection $comments;

    // /**
    //  * @var Collection<int, Applications>
    //  */
    // #[ORM\OneToMany(targetEntity: Applications::class, mappedBy: 'user_candidate')]
    // #[Groups(['read', 'write'])]
    // private Collection $applications;

    /**
     * @var Collection<int, Messages>
     */
    #[ORM\OneToMany(targetEntity: Messages::class, mappedBy: 'user_sender')]
    #[Groups(['read', 'write'])]
    private Collection $messagesSent;

    /**
     * @var Collection<int, Messages>
     */
    #[ORM\OneToMany(targetEntity: Messages::class, mappedBy: 'user_recipient')]
    #[Groups(['read', 'write'])]
    private Collection $messagesReceived;

    /**
     * @var Collection<int, Vehicles>
     */
    #[ORM\OneToMany(targetEntity: Vehicles::class, mappedBy: 'user')]
    #[Groups(['read', 'write'])]
    private Collection $vehicles;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        // $this->applications = new ArrayCollection();
        $this->messagesSent = new ArrayCollection();
        $this->messagesReceived = new ArrayCollection();
        $this->vehicles = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        // $this->yes = new ArrayCollection();
        $this->offers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->mail;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

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
            $comment->setSender($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getSender() === $this) {
                $comment->setSender(null);
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
    //         $application->setUserCandidate($this);
    //     }

    //     return $this;
    // }

    // public function removeApplication(Applications $application): static
    // {
    //     if ($this->applications->removeElement($application)) {
    //         // set the owning side to null (unless already changed)
    //         if ($application->getUserCandidate() === $this) {
    //             $application->setUserCandidate(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, Messages>
     */
    public function getMessagesSent(): Collection
    {
        return $this->messagesSent;
    }

    public function addMessageSent(Messages $messageSent): static
    {
        if (!$this->messagesSent->contains($messageSent)) {
            $this->messagesSent->add($messageSent);
            $messageSent->setUserSender($this);
        }

        return $this;
    }

    public function removeMessageSent(Messages $messageSent): static
    {
        if ($this->messagesSent->removeElement($messageSent)) {
            // set the owning side to null (unless already changed)
            if ($messageSent->getUserSender() === $this) {
                $messageSent->setUserSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Messages>
     */
    public function getMessagesReceived(): Collection
    {
        return $this->messagesReceived;
    }

    public function addMessageReceived(Messages $messageReceived): static
    {
        if (!$this->messagesReceived->contains($messageReceived)) {
            $this->messagesReceived->add($messageReceived);
            $messageReceived->setUserRecipient($this);
        }

        return $this;
    }

    public function removeMessageReceived(Messages $messageReceived): static
    {
        if ($this->messagesReceived->removeElement($messageReceived)) {
            // set the owning side to null (unless already changed)
            if ($messageReceived->getUserRecipient() === $this) {
                $messageReceived->setUserRecipient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Vehicles>
     */
    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function addVehicle(Vehicles $vehicle): static
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles->add($vehicle);
            $vehicle->setUser($this);
        }

        return $this;
    }

    public function removeVehicle(Vehicles $vehicle): static
    {
        if ($this->vehicles->removeElement($vehicle)) {
            // set the owning side to null (unless already changed)
            if ($vehicle->getUser() === $this) {
                $vehicle->setUser(null);
            }
        }

        return $this;
    }

}
