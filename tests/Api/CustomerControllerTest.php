<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\User\Entity\Manager;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CustomerControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManager $entityManager;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
    }

    #[Test]
    public function it_test_add_customer(): void
    {
        $this->itGetsTokenForManager();

        $token = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->request('POST', '/api/customer/add', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token['token'],
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'newcustomer@example.com',
            'password' => 'newpassword',
        ]));

        self::assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('success', $response);
        self::assertStringContainsString('Customer successfully created', $response['success']['data']);
    }

    private function itGetsTokenForManager(): void
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

        $this->client->request('POST', '/authentication', [
        ], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => $email,
            'password' => $plaintextPassword,
        ]));
    }
}
