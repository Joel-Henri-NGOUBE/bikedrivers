<?php

namespace App\Tests\Unit;

use App\Entity\Documents;
use App\Entity\User;
use App\Entity\Vehicles;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testGetAndSetMail(): void
    {
        $this->user->setMail('mail@gmail.com');
        $this->assertEquals('mail@gmail.com', $this->user->getMail());
    }

    public function testGetAndSetRoles(): void
    {
        $this->user->setRoles(['ROLE_ADMIN']);
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $this->user->getRoles());
    }

    public function testGetAndSetPassword(): void
    {
        $this->user->setPassword('mypassword');
        $this->assertEquals('mypassword', $this->user->getPassword());
    }

    public function testGetAndSetFirstname(): void
    {
        $this->user->setFirstname('John');
        $this->assertEquals('John', $this->user->getFirstname());
    }

    public function testGetAndSetLastname(): void
    {
        $this->user->setLastname('Doe');
        $this->assertEquals('Doe', $this->user->getLastname());
    }

    public function testGetAndAddVehicles(): void
    {
        $newVehicle = new Vehicles();
        $this->user->addVehicle($newVehicle);
        $this->assertTrue($this->user->getVehicles()->contains($newVehicle));
    }

    public function testRemoveVehicle(): void
    {
        $newVehicle = new Vehicles();
        $this->user->addVehicle($newVehicle);
        $this->assertTrue($this->user->getVehicles()->contains($newVehicle));
        $this->user->getVehicles()->removeElement($newVehicle);
        $this->assertFalse($this->user->getVehicles()->contains($newVehicle));
    }

    public function testGetAndAddDocuments(): void
    {
        $newDocument = new Documents();
        $this->user->addDocument($newDocument);
        $this->assertTrue($this->user->getDocuments()->contains($newDocument));
    }

    public function testRemoveDocument(): void
    {
        $newDocument = new Documents();
        $this->user->addDocument($newDocument);
        $this->assertTrue($this->user->getDocuments()->contains($newDocument));
        $this->user->getDocuments()->removeElement($newDocument);
        $this->assertFalse($this->user->getDocuments()->contains($newDocument));
    }
}
