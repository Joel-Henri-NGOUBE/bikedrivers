<?php

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

final class SignUpControllerTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testIndex(): void
    {
        $client = static::createClient();
        $response = $client->request('POST', '/signup', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'firstname' => 'Esteban',
                'lastname' => 'Benzoîle',
                'mail' => 'this1@gmail.com',
                'password' => 'password',
            ],
        ]);

        self::assertResponseIsSuccessful();

        $response = $client->request('POST', '/api/login_check', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'email' => 'this1@gmail.com',
                'password' => 'password',
            ],
        ]);

        self::assertResponseIsSuccessful();

    }
}