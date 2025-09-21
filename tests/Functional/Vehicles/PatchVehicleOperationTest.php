<?php

namespace App\Tests\Functional\Vehicles;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

final class PatchVehicleOperationTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testIndex(): void
    {

        $client = static::createClient();

        $container = self::getContainer();

        // Creating an user
        $user = new User();
        $user->setFirstName('Ernest');
        $user->setLastName('Benzoîle');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setMail('this2@gmail.com');
        $user->setPassword(
            $container->get('security.user_password_hasher')->hashPassword($user, 'password')
        );
        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();

        // Authenticating him
        $response1 = $client->request('POST', '/api/login_check', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'mail' => 'this2@gmail.com',
                'password' => 'password',
            ],
        ]);
        $json = $response1->toArray();

        // Getting his id to assert he has been created
        $response2 = $client->request('POST', '/api/id', [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
            'json' => [
                'mail' => 'this2@gmail.com',
            ],
        ]);

        $id = $response2->toArray()['id'];

        $client->request('POST', "/api/users/$id/vehicles", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
            'json' => [
                'type' => 'voiture',
                'model' => 'C454',
                'brand' => 'Renault',
                'purchasedAt' => '2025-08-25'
            ],
        ]);

        $response3 = $client->request('GET', "/api/users/$id/vehicles", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ]
        ]);

        $vehicles = $response3->toArray()['member'][0];
        $vehicleId = $vehicles['id'];

        $this->assertResponseIsSuccessful();
        $this->assertEquals('Renault', $vehicles['brand']);

        
        $client->request('PATCH', "/api/users/$id/vehicles/$vehicleId", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
                'Content-Type' => 'application/merge-patch+json',
            ],
            'json' => [
                'brand' => 'Hyundai',
            ],
        ]);

        $response4 = $client->request('GET', "/api/users/$id/vehicles", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals('Hyundai', $response4->toArray()['member'][0]['brand']);

    }
}