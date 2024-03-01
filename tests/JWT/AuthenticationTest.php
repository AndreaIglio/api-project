<?php

declare(strict_types=1);

namespace App\Tests\JWT;

use App\User\Entity\Manager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AuthenticationTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_should_verify_the_authentication_with_jwt(): void
    {
        $client = self::createClient();
        $userPasswordHasher = self::getContainer()->get('app.hasher.user_password');
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        $email = 'test@example.com';
        $plaintextPassword = 'p4ssw0rd';

        $user = new Manager(
            $plaintextPassword,
            $email,
        );

        $userPasswordHasher->setHashedPassword($user, $plaintextPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        // retrieve a token
        $response = $client->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' => $plaintextPassword,
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        $client->request('GET', '/api/agenzia_installatrices');
        $this->assertResponseStatusCodeSame(401);

        // test authorized
        $client->request('GET', '/api/agenzia_installatrices', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }
}
