<?php

namespace App\Tests\Unit;

use App\Entity\Documents;
use App\Entity\Enums\State;
use App\Entity\MatchDocuments;
use App\Entity\RequiredDocuments;
use PHPUnit\Framework\TestCase;

final class MatchDocumentsTest extends TestCase
{
    private MatchDocuments $matchDocument;

    protected function setUp(): void
    {
        $this->matchDocument = new MatchDocuments();
    }

    public function testGetAndSetState(): void
    {
        $this->matchDocument->setState(State::Valid);
        $this->assertEquals(State::Valid, $this->matchDocument->getState());
    }

    public function testGetAndSetDocument(): void
    {
        $newDocument = new Documents();
        $this->matchDocument->setDocument($newDocument);
        $this->assertEquals($newDocument, $this->matchDocument->getDocument());
    }

    public function testGetAndSetRequiredDocument(): void
    {
        $newRequiredDocument = new RequiredDocuments();
        $this->matchDocument->setRequiredDocument($newRequiredDocument);
        $this->assertEquals($newRequiredDocument, $this->matchDocument->getRequiredDocument());
    }
}
