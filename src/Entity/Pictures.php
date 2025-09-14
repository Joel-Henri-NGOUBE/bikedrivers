<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Controller\PicturesController;
use App\Repository\PicturesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PicturesRepository::class)]
#[Vich\Uploadable]
#[ApiResource(
    uriTemplate: 'users/{user_id}/vehicles/{vehicle_id}/pictures',
    operations: [new Post()],
    controller: PicturesController::class
)]

// Defines the route that gets an operation
#[ApiResource(
    uriTemplate: '/users/{user_id}/vehicles/{vehicle_id}/pictures',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toProperty: 'user'),
        'vehicle_id' => new Link(fromClass: Vehicles::class, toProperty: 'vehicle'),
    ],
    operations: [new GetCollection()]
)]

#[ApiResource(
    uriTemplate: '/vehicles/{vehicle_id}/pictures',
    uriVariables: [
        'vehicle_id' => new Link(fromClass: Vehicles::class, toProperty: 'vehicle'),
    ],
    security: "is_granted('PUBLIC_ACCESS')",
    operations: [new GetCollection()]
)]

#[ApiResource(
    uriTemplate: '/users/{user_id}/vehicles/{vehicle_id}/pictures/{picture_id}',
    uriVariables: [
        'user_id' => new Link(fromClass: User::class, toClass: Vehicles::class, fromProperty: 'vehicles'),
        'vehicle_id' => new Link(fromClass: Vehicles::class, toProperty: 'vehicle'),
        'picture_id' => new Link(fromClass: Pictures::class),
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

class Pictures
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $path = null;

    #[Vich\UploadableField(mapping: 'pictures', fileNameProperty: 'path')]
    private ?File $pictureFile = null;

    #[ORM\Column(nullable: false)]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\ManyToOne(inversedBy: 'pictures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicles $vehicle = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->setAddedAt();
        $this->setUpdatedAt();
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

    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }

    public function setPictureFile(File $file): static
    {
        $this->pictureFile = $file;

        if ($file) {
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

    public function getVehicle(): ?Vehicles
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicles $vehicle): static
    {
        $this->vehicle = $vehicle;

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
}
