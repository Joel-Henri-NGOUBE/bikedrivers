<?php

namespace App\Tests\Unit;

use App\Entity\Applications;
use App\Entity\Documents;
use App\Entity\MatchDocuments;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

final class DocumentsTest extends TestCase
{
    private Documents $document;

    protected function setUp(): void
    {
        $this->document = new Documents();
    }

    public function testGetAndSetPath(): void
    {
        $this->document->setPath('mydocument.pdf');
        $this->assertEquals('mydocument.pdf', $this->document->getPath());
    }

    public function testGetAndSetDocumentFile(): void
    {
        $documentFile = new File(__DIR__ . '/../Files/REAC_CDA_V04_24052023.pdf', 'REAC_CDA_V04_24052023.pdf');
        $this->document->setDocumentFile($documentFile);
        $this->assertEquals($documentFile, $this->document->getDocumentFile());
    }

    public function testGetAndAddApplications(): void
    {
        $newApplication = new Applications();
        $this->document->addApplication($newApplication);
        $this->assertTrue($this->document->getApplications()->contains($newApplication));
    }

    public function testRemoveApplication(): void
    {
        $newApplication = new Applications();
        $this->document->addApplication($newApplication);
        $this->assertTrue($this->document->getApplications()->contains($newApplication));
        $this->document->getApplications()->removeElement($newApplication);
        $this->assertFalse($this->document->getApplications()->contains($newApplication));
    }

    public function testGetAndAddMatchDocuments(): void
    {
        $newMatchDocument = new MatchDocuments();
        $this->document->addMatchDocument($newMatchDocument);
        $this->assertTrue($this->document->getMatchDocuments()->contains($newMatchDocument));
    }

    public function testRemoveMatchDocument(): void
    {
        $newMatchDocument = new MatchDocuments();
        $this->document->addMatchDocument($newMatchDocument);
        $this->assertTrue($this->document->getMatchDocuments()->contains($newMatchDocument));
        $this->document->getMatchDocuments()->removeElement($newMatchDocument);
        $this->assertFalse($this->document->getMatchDocuments()->contains($newMatchDocument));
    }

    public function testGetAndSetUser(): void
    {
        $newUser = new User();
        $this->document->setUser($newUser);
        $this->assertEquals($newUser, $this->document->getUser());
    }
}
