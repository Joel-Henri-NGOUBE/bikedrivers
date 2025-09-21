<?php

namespace App\Tests\Unit;

use App\Entity\MatchDocuments;
use App\Entity\Offers;
use App\Entity\RequiredDocuments;
use PHPUnit\Framework\TestCase;

final class RequiredDocumentsTest extends TestCase
{
    private RequiredDocuments $requiredDocument;

    protected function setUp(): void
    {
        $this->requiredDocument = new RequiredDocuments();
    }

    public function testGetAndSetInformations(): void
    {
        $this->requiredDocument->setInformations('Your document must last less than 3 months');
        $this->assertEquals('Your document must last less than 3 months', $this->requiredDocument->getInformations());
    }

    public function testGetAndSetName(): void
    {
        $this->requiredDocument->setName('Fiche de paie');
        $this->assertEquals('Fiche de paie', $this->requiredDocument->getName());
    }

    public function testGetAndAddMatchDocuments(): void
    {
        $newMatchDocument = new MatchDocuments();
        $this->requiredDocument->addMatchDocument($newMatchDocument);
        $this->assertTrue($this->requiredDocument->getMatchDocuments()->contains($newMatchDocument));
    }

    public function testRemoveMatchDocument(): void
    {
        $newMatchDocument = new MatchDocuments();
        $this->requiredDocument->addMatchDocument($newMatchDocument);
        $this->assertTrue($this->requiredDocument->getMatchDocuments()->contains($newMatchDocument));
        $this->requiredDocument->getMatchDocuments()->removeElement($newMatchDocument);
        $this->assertFalse($this->requiredDocument->getMatchDocuments()->contains($newMatchDocument));
    }

    public function testGetAndSetOffer(): void
    {
        $newOffer = new Offers();
        $this->requiredDocument->setOffer($newOffer);
        $this->assertEquals($newOffer, $this->requiredDocument->getOffer());
    }
}
