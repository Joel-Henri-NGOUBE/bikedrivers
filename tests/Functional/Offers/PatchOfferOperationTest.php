<?php

namespace App\Tests\Functional\Offers;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

final class PatchOfferOperationTest extends ApiTestCase
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

        // Getting the vehciles list of the user
        $response3 = $client->request('GET', "/api/users/{$id}/vehicles", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, count($response3->toArray()['member']));

        // Creating a new vehicle
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

        // Fetching the vehicles list
        $response4 = $client->request('GET', "/api/users/{$id}/vehicles", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        $json1 = $response4->toArray()['member'];

        $vehicle_id = $json1[0]['id'];

        // Creating an offer for th ecreated vehicle
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

        // Getting the vehicles' offers
        $response5 = $client->request('GET', "/api/users/{$id}/vehicles/{$vehicle_id}/offers", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        $offer_id = $response5->toArray()['member'][0]['id'];

        // Setting the offer created
        $client->request('PATCH', "/api/users/{$id}/vehicles/{$vehicle_id}/offers/{$offer_id}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
                'Content-Type' => 'application/merge-patch+json',
            ],
            'json' => [
                'price' => 9000,
            ],
        ]);

        $response6 = $client->request('GET', "/api/users/{$id}/vehicles/{$vehicle_id}/offers", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        $json3 = $response6->toArray()['member'];
        $this->assertResponseIsSuccessful();
        // Asserting the data has been updated after requesting the offer
        $this->assertEquals(9000, $json3[0]['price']);

    }
}
