<?php

namespace App\Tests\Functional\Vehicles;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Entity\Documents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

final class PostAndGetDocumentsOperationTest extends ApiTestCase
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

        $document = new UploadedFile(__DIR__ . '/../../Files/REAC_CDA_V04_FILE_TESTING_DOCUMENT.pdf', 'REAC_CDA_V04_FILE_TESTING_DOCUMENT.pdf');

        $response3 = $client->request('GET', "/api/users/$id/documents", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, count($response3->toArray()['member']));

        $client->request('POST', "/api/users/$id/documents", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
                'Content-Type' => 'multipart/form-data'
            ],
            'extra' => [
                'files' => [
                    'file' => $document,
                ]
            ],
        ]);
        $this->assertResponseIsSuccessful();
        // $this->assertMatchesResourceItemJsonSchema(Documents::class);

        $response4 = $client->request('GET', "/api/users/$id/documents", [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, count($response4->toArray()['member']));



    }
}