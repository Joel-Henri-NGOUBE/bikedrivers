<?php

namespace App\Tests\Functional\Offers;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

final class PostAndGetOffersOperationTest extends ApiTestCase
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
        $user->setMail('this2@gmail.com');
        $user->setRoles(['ROLE_ADMIN']);
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

        // Getting the users' vehicles 
        $response3 = $client->request('GET', "/api/users/{$id}/vehicles", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, count($response3->toArray()['member']));

        // Adding him a vehicle
        $client->request('POST', "/api/users/{$id}/vehicles", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
            'json' => [
                'type' => 'voiture',
                'model' => 'C454',
                'brand' => 'Renault',
                'purchasedAt' => '2025-08-25',
            ],
        ]);

        // Getting his vehicles list
        $response4 = $client->request('GET', "/api/users/{$id}/vehicles", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        $json1 = $response4->toArray()['member'];

        $this->assertEquals(1, count($json1));

        $vehicle_id = $json1[0]['id'];

        // Getting the offers of the vehicle
        $response5 = $client->request('GET', "/api/users/{$id}/vehicles/{$vehicle_id}/offers", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        $json2 = $response5->toArray()['member'];
        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, count($json2));

        // Creating a new offer
        $client->request('POST', "/api/users/{$id}/vehicles/{$vehicle_id}/offers", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
            'json' => [
                'title' => 'The title.',
                'description' => 'The description ',
                'price' => 10500,
                'service' => 'SALE',
                'user' => "/api/users/{$id}",
                'vehicle' => "api/vehicles/{$vehicle_id}",
            ],
        ]);

        $response6 = $client->request('GET', "/api/users/{$id}/vehicles/{$vehicle_id}/offers", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        $json3 = $response6->toArray()['member'];
        $this->assertResponseIsSuccessful();
        // Asserting it has been added after having requesting all the offers
        $this->assertEquals(1, count($json3));

    }
}
