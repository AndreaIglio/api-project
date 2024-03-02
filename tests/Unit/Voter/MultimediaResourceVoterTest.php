<?php

declare(strict_types=1);

namespace App\Tests\Unit\Voter;

use App\MultimediaResource\Entity\MultimediaResource;
use App\MultimediaResource\Voter\MultimediaResourceVoter;
use App\User\Entity\Admin;
use App\User\Entity\Common\User;
use App\User\Entity\Customer;
use App\User\Entity\Manager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class MultimediaResourceVoterTest extends TestCase
{
    private VoterInterface $voter;

    protected function setUp(): void
    {
        $this->voter = new MultimediaResourceVoter();
    }

    #[Test]
    public function all_roles_can_view_resources(): void
    {
        $roles = ['ROLE_ADMIN', 'ROLE_MANAGER', 'ROLE_CUSTOMER'];

        foreach ($roles as $role) {
            $user = $this->createUserWithRole($role);
            $token = $this->createMockToken($user);

            $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($token, null, [MultimediaResourceVoter::VIEW]));
        }
    }

    #[Test]
    public function only_customer_can_create_or_remove_resources(): void
    {
        $customerUser = $this->createCustomerUser();
        $customerToken = $this->createMockToken($customerUser);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($customerToken, null, [MultimediaResourceVoter::CREATE]));

        $nonCustomerRoles = ['ROLE_ADMIN', 'ROLE_MANAGER'];

        foreach ($nonCustomerRoles as $role) {
            $user = $this->createUserWithRole($role);
            $token = $this->createMockToken($user);

            $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($token, null, [MultimediaResourceVoter::CREATE]));
        }
    }

    #[Test]
    public function admin_can_edit_or_remove_any_resources(): void
    {
        $adminUser = $this->createAdminUser();
        $customerUser = $this->createCustomerUser();
        $adminToken = $this->createMockToken($adminUser);
        $resource = new MultimediaResource($customerUser);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($adminToken, $resource, [MultimediaResourceVoter::EDIT_OR_REMOVE]));
    }

    #[Test]
    public function customer_can_edit_or_remove_their_resources(): void
    {
        $customerUser = $this->createCustomerUser();
        $customerToken = $this->createMockToken($customerUser);
        $resource = new MultimediaResource($customerUser);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($customerToken, $resource, [MultimediaResourceVoter::EDIT_OR_REMOVE]));
    }

    #[Test]
    public function customer_cannot_edit_or_remove_others_resources(): void
    {
        $customerUser = $this->createCustomerUser();
        $otherCustomerUser = new Customer(new Manager('otherManager', 'otherManager@example.it'), 'otherCustomer', 'otherCustomer@example.it');
        $customerToken = $this->createMockToken($customerUser);
        $resource = new MultimediaResource($otherCustomerUser);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($customerToken, $resource, [MultimediaResourceVoter::EDIT_OR_REMOVE]));
    }

    private function createUserWithRole(string $role): User
    {
        switch ($role) {
            case 'ROLE_ADMIN':
                return new Admin('admin', 'admin@example.it');
            case 'ROLE_MANAGER':
                return new Manager('manager', 'manager@example.it');
            case 'ROLE_CUSTOMER':
                return new Customer(new Manager('manager', 'manager@example.it'), 'customer', 'customer@example.it');
            default:
                throw new \InvalidArgumentException("Invalid role: {$role}");
        }
    }

    private function createAdminUser(): User
    {
        return new Admin('admin', 'admin@example.it');
    }

    private function createManagerUser(): User
    {
        return new Manager('manager', 'manager@example.it');
    }

    private function createCustomerUser(): User
    {
        return new Customer(new Manager('manager', 'manager@example.it'), 'customer', 'customer@example.it');
    }

    private function createMockToken(User $user): TokenInterface
    {
        $mock = $this->createMock(TokenInterface::class);
        $mock->method('getUser')->willReturn($user);

        return $mock;
    }
}
