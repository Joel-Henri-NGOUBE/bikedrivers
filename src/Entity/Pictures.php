<?php

namespace App\Entity;

use App\Repository\PicturesRepository;
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
use App\Controller\PicturesController;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PicturesRepository::class)]
#[Vich\Uploadable]
#[ApiResource(
    uriTemplate: 'users/{user_id}/vehicles/{vehicle_id}/pictures',
    // uriVariables: [
    //     'vehicle_id' => new Link(fromClass: Vehicles::class, toProperty: 'vehicle')
    // ],
    operations: [new Post()],
    security: "is_granted('PUBLIC_ACCESS')",
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

// #[ApiResource(
//     uriTemplate: '/users/{user_id}/vehicles/{vehicle_id}/pictures/{picture_id}',
//     uriVariables: [
//         'user_id' => new Link(fromClass: User::class, toClass: Vehicles::class, fromProperty: 'vehicles'),
//         'vehicle_id' => new Link(fromClass: Vehicles::class, toProperty: 'vehicle'),
//         'picture_id' => new Link(fromClass: Pictures::class),
//     ],
//     operations: [new Get()]
// )]

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

    #[ORM\Column]
    private ?\DateTimeImmutable $AddedAt = null;

    #[ORM\ManyToOne(inversedBy: 'pictures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicles $vehicle = null;

    public function __construct()
    {
        $this->AddedAt = new \DateTimeImmutable();
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

    public function getVehicle(): ?Vehicles
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicles $vehicle): static
    {
        $this->vehicle = $vehicle;

        return $this;
    }
}
