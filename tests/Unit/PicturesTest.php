<?php

namespace App\Tests\Unit;

use App\Entity\Pictures;
use App\Entity\Vehicles;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

final class PicturesTest extends TestCase
{
    private Pictures $picture;

    protected function setUp(): void
    {
        $this->picture = new Pictures();
    }

    public function testGetAndSetPath()
    {
        $this->picture->setPath('mypicture.jpeg');
        $this->assertEquals('mypicture.jpeg', $this->picture->getPath());
    }

    public function testGetAndSetPictureFile()
    {
        $pictureFile = new File(__DIR__ . '/../Files/Renault-Clio-5-occasion.jpg', 'Renault-Clio-5-occasion.jpg');
        $this->picture->setPictureFile($pictureFile);
        $this->assertEquals($pictureFile, $this->picture->getPictureFile());
    }

    public function testGetAndSetVehicle()
    {
        $newVehicle = new Vehicles();
        $this->picture->setVehicle($newVehicle);
        $this->assertEquals($newVehicle, $this->picture->getVehicle());
    }
}
