<?php

namespace App\Tests\Unit;

use App\Entity\Applications;
use App\Entity\Enums\Service;
use App\Entity\Enums\Status;
use App\Entity\Offers;
use App\Entity\RequiredDocuments;
use App\Entity\Vehicles;
use PHPUnit\Framework\TestCase;

final class OffersTest extends TestCase
{
    private Offers $offer;

    protected function setUp(): void
    {
        $this->offer = new Offers();
    }

    public function testGetAndSetIdTaker()
    {
        $this->offer->setIdTaker(2);
        $this->assertEquals(2, $this->offer->getIdTaker());
    }

    public function testGetAndSetDescription()
    {
        $this->offer->setDescription("I'm describing my car");
        $this->assertEquals("I'm describing my car", $this->offer->getDescription());
    }

    public function testGetAndSetStatus()
    {
        $this->offer->setStatus(Status::Inactive);
        $this->assertEquals(Status::Inactive, $this->offer->getStatus());
    }

    public function testGetAndSetStartsAt()
    {
        $startsAt = new \DateTimeImmutable();
        $this->offer->setStartsAt($startsAt);
        $this->assertEquals($startsAt, $this->offer->getStartsAt());
    }

    public function testGetAndSetEndsAt()
    {
        $endsAt = new \DateTimeImmutable();
        $this->offer->setEndsAt($endsAt);
        $this->assertEquals($endsAt, $this->offer->getEndsAt());
    }

    public function testGetAndSetTitle()
    {
        $this->offer->setTitle('My car is the best');
        $this->assertEquals('My car is the best', $this->offer->getTitle());
    }

    public function testGetAndSetPrice()
    {
        $this->offer->setPrice(12000);
        $this->assertEquals(12000, $this->offer->getPrice());
    }

    public function testGetAndSetService()
    {
        $this->offer->setService(Service::Sale);
        $this->assertEquals(Service::Sale, $this->offer->getService());
    }

    public function testGetAndAddApplications()
    {
        $newApplication = new Applications();
        $this->offer->addApplication($newApplication);
        $this->assertTrue($this->offer->getApplications()->contains($newApplication));
    }

    public function testRemoveApplication()
    {
        $newApplication = new Applications();
        $this->offer->addApplication($newApplication);
        $this->assertTrue($this->offer->getApplications()->contains($newApplication));
        $this->offer->getApplications()->removeElement($newApplication);
        $this->assertFalse($this->offer->getApplications()->contains($newApplication));
    }

    public function testGetAndAddRequiredDocuments()
    {
        $newRequiredDocument = new RequiredDocuments();
        $this->offer->addRequiredDocument($newRequiredDocument);
        $this->assertTrue($this->offer->getRequiredDocuments()->contains($newRequiredDocument));
    }

    public function testRemoveRequiredDocument()
    {
        $newRequiredDocument = new RequiredDocuments();
        $this->offer->addRequiredDocument($newRequiredDocument);
        $this->assertTrue($this->offer->getRequiredDocuments()->contains($newRequiredDocument));
        $this->offer->getRequiredDocuments()->removeElement($newRequiredDocument);
        $this->assertFalse($this->offer->getRequiredDocuments()->contains($newRequiredDocument));
    }

    public function testGetAndSetVehicle()
    {
        $newVehicle = new Vehicles();
        $this->offer->setVehicle($newVehicle);
        $this->assertEquals($newVehicle, $this->offer->getVehicle());
    }
}
