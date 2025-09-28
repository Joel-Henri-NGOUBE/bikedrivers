<?php

namespace App\Tests\Functional\Applications;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class PostAndGetApplicationsOperationTest extends ApiTestCase
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

        $user2 = new User();
        $user2->setFirstName('Ernesto');
        $user2->setLastName('Benzoîlo');
        $user2->setMail('this@gmail.com');
        $user2->setPassword(
            $container->get('security.user_password_hasher')->hashPassword($user2, 'password')
        );
        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user2);
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

        // Authenticating the second user
        $responseUser2_1 = $client->request('POST', '/api/login_check', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'mail' => 'this2@gmail.com',
                'password' => 'password',
            ],
        ]);
        $jsonUser2 = $responseUser2_1->toArray();

        // Getting his id to assert he has been logged in
        $responseUser2_2 = $client->request('POST', '/api/id', [
            'headers' => [
                'Authorization' => 'Bearer ' . $jsonUser2['token'],
            ],
            'json' => [
                'mail' => 'this2@gmail.com',
            ],
        ]);

        $id = $response2->toArray()['id'];

        $idUser2 = $responseUser2_2->toArray()['id'];

        // Requesting the Admin vehicles
        $response3 = $client->request('GET', "/api/users/{$id}/vehicles", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, count($response3->toArray()['member']));

        // Adding a new vehicle to the list
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

        // Getting the vehicles' lsit of the Admin
        $response4 = $client->request('GET', "/api/users/{$id}/vehicles", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        $json1 = $response4->toArray()['member'];

        $this->assertEquals(1, count($json1));

        $vehicle_id = $json1[0]['id'];

        // Creating an offer for that vehicle
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

        $offer_id = $json3[0]['id'];

        // Getting the required documents associated to the offer
        $response6 = $client->request('GET', "/api/offers/{$offer_id}/required_documents", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        $json3 = $response6->toArray()['member'];

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, count($json3));

        // Adding a required document for that offer
        $client->request('POST', "/api/offers/{$offer_id}/required_documents", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
            'json' => [
                'name' => 'Titre de séjour',
                'offer' => "api/offers/{$offer_id}",
            ],
        ]);

        // Adding the added required document
        $response7 = $client->request('GET', "/api/offers/{$offer_id}/required_documents", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        // Finding if the second user has applied
        $hasApplied = $client->request('GET', "/api/offers/{$offer_id}/applications/users/{$idUser2}/hasApplied", [
            'headers' => [
                'Authorization' => 'Bearer ' . $jsonUser2['token'],
            ],
        ]);

        $this->assertFalse($hasApplied->toArray()['hasApplied']);

        $required_document_id = $response7->toArray()['member'][0]['id'];

        $document = new UploadedFile(__DIR__ . '/../../Files/REAC_CDA_V04_FILE_TESTING_DOCUMENT_2.pdf', 'REAC_CDA_V04_FILE_TESTING_DOCUMENT_2.pdf');

        // Uploading a new document for the second user
        $client->request('POST', "/api/users/{$idUser2}/documents", [
            'headers' => [
                'Authorization' => 'Bearer ' . $jsonUser2['token'],
                'Content-Type' => 'multipart/form-data',
            ],
            'extra' => [
                'files' => [
                    'file' => $document,
                ],
            ],
        ]);

        // Getting his list of documents
        $response8 = $client->request('GET', "/api/users/{$idUser2}/documents", [
            'headers' => [
                'Authorization' => 'Bearer ' . $jsonUser2['token'],
            ],
        ]);

        $document_id = $response8->toArray()['member'][0]['id'];

        // Associating the document with a required document
        $client->request('POST', "/api/required_documents/{$required_document_id}/documents/{$document_id}/match_documents", [
            'headers' => [
                'Authorization' => 'Bearer ' . $jsonUser2['token'],
            ],
        ]);

        $this->assertResponseIsSuccessful();

        // Creating an application for the offer
        $client->request('POST', "/api/offers/{$offer_id}/applications", [
            'headers' => [
                'Authorization' => 'Bearer ' . $jsonUser2['token'],
            ],
            'json' => [
                'offer' => "/api/offers/{$offer_id}",
                'documents' => ["/api/documents/{$document_id}"],
            ],
        ]);
        $this->assertResponseIsSuccessful();

        // Finding if the user has applied
        $response9 = $client->request('GET', "/api/offers/{$offer_id}/applications/users/{$idUser2}/hasApplied", [
            'headers' => [
                'Authorization' => 'Bearer ' . $jsonUser2['token'],
            ],
        ]);

        $this->assertTrue($response9->toArray()['hasApplied']);

        // Finding the list of appliers to the previously set offer
        $response10 = $client->request('GET', "/api/offers/{$offer_id}/applications/appliers", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        // Asserting ther is one applicant
        $this->assertEquals(1, count($response10->toArray()));
    }
}
