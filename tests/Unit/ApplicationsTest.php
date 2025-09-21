<?php

namespace App\Tests\Unit;

use App\Entity\Offers;
use App\Entity\Documents;
use App\Entity\Applications;
use App\Entity\Enums\ApplicationState;
use PHPUnit\Framework\TestCase;


final class ApplicationsTest extends TestCase
{
    private Applications $application;

    public function setUp(): void
    {
        $this->application = new Applications();
    }

    public function testGetAndSetState(){
        $this->application->setState(ApplicationState::Accepted);
        $this->assertEquals(ApplicationState::Accepted, $this->application->getState());
    }

    public function testGetAndSetOffer(){
        $newOffer = new Offers();
        $this->application->setOffer($newOffer);
        $this->assertEquals($newOffer, $this->application->getOffer());
    }

    public function testGetAndAddDocuments(){
        $newDocument = new Documents();
        $this->application->addDocument($newDocument);
        $this->assertTrue($this->application->getDocuments()->contains($newDocument));
    }

    public function testRemoveDocument(){
        $newDocument = new Documents();
        $this->application->addDocument($newDocument);
        $this->assertTrue($this->application->getDocuments()->contains($newDocument));
        $this->application->getDocuments()->removeElement($newDocument);
        $this->assertFalse($this->application->getDocuments()->contains($newDocument));
    }
}