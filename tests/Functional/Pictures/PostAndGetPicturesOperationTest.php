<?php

namespace App\Tests\Functional\Vehicles;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class PostAndGetPicturesOperationTest extends ApiTestCase
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

        $picture = new UploadedFile(__DIR__ . '/../../Files/Renault-Clio-5-occasion_FILE_TESTING_PICTURE.jpg', 'Renault-Clio-5-occasion_FILE_TESTING_PICTURE.jpg');

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

        $response4 = $client->request('GET', "/api/users/{$id}/vehicles", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        $vehicle_id = $response4->toArray()['member'][0]['id'];

        $response5 = $client->request('GET', "/api/users/{$id}/vehicles/{$vehicle_id}/pictures", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, count($response5->toArray()['member']));

        $client->request('POST', "/api/users/{$id}/vehicles/{$vehicle_id}/pictures", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
                'Content-Type' => 'multipart/form-data',
            ],
            'extra' => [
                'files' => [
                    'file' => $picture,
                ],
            ],
        ]);

        $response6 = $client->request('GET', "/api/users/{$id}/vehicles/{$vehicle_id}/pictures", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, count($response6->toArray()['member']));

    }
}
