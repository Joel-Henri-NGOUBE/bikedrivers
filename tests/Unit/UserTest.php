<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Entity\Vehicles;
use App\Entity\Documents;
use PHPUnit\Framework\TestCase;


final class UserTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        $this->user = new User();
    }

    public function testGetAndSetMail(){
        $this->user->setMail("mail@gmail.com");
        $this->assertEquals("mail@gmail.com", $this->user->getMail());
    }

    public function testGetAndSetRoles(){
        $this->user->setRoles(["ROLE_ADMIN"]);
        $this->assertEquals(["ROLE_ADMIN", "ROLE_USER"], $this->user->getRoles());
    }

    public function testGetAndSetPassword(){
        $this->user->setPassword("mypassword");
        $this->assertEquals("mypassword", $this->user->getPassword());
    }

    public function testGetAndSetFirstname(){
        $this->user->setFirstname("John");
        $this->assertEquals("John", $this->user->getFirstname());
    }

    public function testGetAndSetLastname(){
        $this->user->setLastname("Doe");
        $this->assertEquals("Doe", $this->user->getLastname());
    }

    public function testGetAndAddVehicles(){
        $newVehicle = new Vehicles();
        $this->user->addVehicle($newVehicle);
        $this->assertTrue($this->user->getVehicles()->contains($newVehicle));
    }

    public function testRemoveVehicle(){
        $newVehicle = new Vehicles();
        $this->user->addVehicle($newVehicle);
        $this->assertTrue($this->user->getVehicles()->contains($newVehicle));
        $this->user->getVehicles()->removeElement($newVehicle);
        $this->assertFalse($this->user->getVehicles()->contains($newVehicle));
    }

    public function testGetAndAddDocuments(){
        $newDocument = new Documents();
        $this->user->addDocument($newDocument);
        $this->assertTrue($this->user->getDocuments()->contains($newDocument));
    }

    public function testRemoveDocument(){
        $newDocument = new Documents();
        $this->user->addDocument($newDocument);
        $this->assertTrue($this->user->getDocuments()->contains($newDocument));
        $this->user->getDocuments()->removeElement($newDocument);
        $this->assertFalse($this->user->getDocuments()->contains($newDocument));
    }
}