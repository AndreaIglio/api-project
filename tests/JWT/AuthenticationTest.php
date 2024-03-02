<?php

declare(strict_types=1);

namespace App\Tests\JWT;

use App\User\Entity\Manager;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AuthenticationTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManager $entityManager;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }

    #[Test]
    public function it_should_verify_the_authentication_with_jwt(): void
    {
        $userPasswordHasher = self::getContainer()->get('app.hasher.user_password');

        $email = 'manager@example.com';
        $plaintextPassword = 'p4ssw0rd';

        $user = new Manager(
            $plaintextPassword,
            $email,
        );

        $userPasswordHasher->setHashedPassword($user, $plaintextPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->request('POST', '/authentication', [
        ], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => $email,
            'password' => $plaintextPassword,
        ]));
        $json = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('token', $json);

        // test not authorized
        $this->client->request('GET', '/api/multimedia-resource/show');
        self::assertResponseStatusCodeSame(401);

        // test authorized
        $this->client->request('GET', '/api/multimedia-resource/show', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$json['token'],
        ]);
        $this->assertResponseIsSuccessful();
    }
}
