<?php

namespace App\Tests\Unit;

use App\Entity\Offers;
use App\Entity\Pictures;
use App\Entity\User;
use App\Entity\Vehicles;
use PHPUnit\Framework\TestCase;

final class VehiclesTest extends TestCase
{
    private Vehicles $vehicle;

    protected function setUp(): void
    {
        $this->vehicle = new Vehicles();
    }

    public function testGetAndSetType(): void
    {
        $this->vehicle->setType('Voiture');
        $this->assertEquals('Voiture', $this->vehicle->getType());
    }

    public function testGetAndSetModel(): void
    {
        $this->vehicle->setModel('Clio 5');
        $this->assertEquals('Clio 5', $this->vehicle->getModel());
    }

    public function testGetAndSetBrand(): void
    {
        $this->vehicle->setBrand('Renault');
        $this->assertEquals('Renault', $this->vehicle->getBrand());
    }

    public function testGetAndSetPurchasedAt(): void
    {
        $purchasedAt = new \DateTimeImmutable();
        $this->vehicle->setPurchasedAt($purchasedAt);
        $this->assertEquals($purchasedAt, $this->vehicle->getPurchasedAt());
    }

    public function testGetAndAddOffers(): void
    {
        $newOffer = new Offers();
        $this->vehicle->addOffer($newOffer);
        $this->assertTrue($this->vehicle->getOffers()->contains($newOffer));
    }

    public function testRemoveOffer(): void
    {
        $newOffer = new Offers();
        $this->vehicle->addOffer($newOffer);
        $this->assertTrue($this->vehicle->getOffers()->contains($newOffer));
        $this->vehicle->getOffers()->removeElement($newOffer);
        $this->assertFalse($this->vehicle->getOffers()->contains($newOffer));
    }

    public function testGetAndAddPictures(): void
    {
        $newPicture = new Pictures();
        $this->vehicle->addPicture($newPicture);
        $this->assertTrue($this->vehicle->getPictures()->contains($newPicture));
    }

    public function testRemovePicture(): void
    {
        $newPicture = new Pictures();
        $this->vehicle->addPicture($newPicture);
        $this->assertTrue($this->vehicle->getPictures()->contains($newPicture));
        $this->vehicle->getPictures()->removeElement($newPicture);
        $this->assertFalse($this->vehicle->getPictures()->contains($newPicture));
    }

    public function testGetAndSetUser(): void
    {
        $newUser = new User();
        $this->vehicle->setUser($newUser);
        $this->assertEquals($newUser, $this->vehicle->getUser());
    }
}
