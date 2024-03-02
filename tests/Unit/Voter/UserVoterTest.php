<?php

declare(strict_types=1);

namespace App\Tests\Unit\Voter;

use App\User\Entity\Admin;
use App\User\Entity\Common\User;
use App\User\Entity\Customer;
use App\User\Entity\Manager;
use App\User\Voter\UserVoter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class UserVoterTest extends TestCase
{
    private VoterInterface $voter;

    protected function setUp(): void
    {
        $this->voter = new UserVoter();
    }

    #[Test]
    public function admin_can_create_manager(): void
    {
        $adminUser = $this->createAdminUser();
        $adminToken = $this->createMockToken($adminUser);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($adminToken, null, [UserVoter::CREATE_MANAGER]));
    }

    #[Test]
    public function customer_cannot_create_manager(): void
    {
        $customerUser = $this->createCustomerUser();
        $customerUser = $this->createMockToken($customerUser);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($customerUser, null, [UserVoter::CREATE_MANAGER]));
    }

    #[Test]
    public function manager_can_create_customer(): void
    {
        $managerUser = $this->createManagerUser();
        $managerToken = $this->createMockToken($managerUser);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($managerToken, null, [UserVoter::CREATE_CUSTOMER]));
    }

    #[Test]
    public function admin_cannot_create_customer_directly(): void
    {
        $adminUser = $this->createAdminUser();
        $adminToken = $this->createMockToken($adminUser);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($adminToken, null, [UserVoter::CREATE_CUSTOMER]));
    }

    #[Test]
    public function admin_can_edit_or_remove_manager(): void
    {
        $adminUser = $this->createAdminUser();
        $managerUser = $this->createManagerUser();
        $adminToken = $this->createMockToken($adminUser);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($adminToken, $managerUser, [UserVoter::EDIT_OR_REMOVE_MANAGER]));
    }

    #[Test]
    public function manager_can_edit_or_remove_themselves(): void
    {
        $managerUser = $this->createManagerUser();
        $managerToken = $this->createMockToken($managerUser);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($managerToken, $managerUser, [UserVoter::EDIT_OR_REMOVE_MANAGER]));
    }

    #[Test]
    public function manager_cannot_edit_or_remove_other_manager(): void
    {
        $managerUser = $this->createManagerUser();
        $otherManagerUser = new Manager('othermanager', 'othermanager@example.it');
        $managerToken = $this->createMockToken($managerUser);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($managerToken, $otherManagerUser, [UserVoter::EDIT_OR_REMOVE_MANAGER]));
    }

    #[Test]
    public function admin_can_edit_or_remove_customer(): void
    {
        $adminUser = $this->createAdminUser();
        $customerUser = $this->createCustomerUser();
        $adminToken = $this->createMockToken($adminUser);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($adminToken, $customerUser, [UserVoter::EDIT_OR_REMOVE_CUSTOMER]));
    }

    #[Test]
    public function manager_can_edit_or_remove_their_customer(): void
    {
        $managerUser = $this->createManagerUser();
        $customerUser = new Customer($managerUser, 'customer', 'customer@example.it');
        $managerToken = $this->createMockToken($managerUser);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($managerToken, $customerUser, [UserVoter::EDIT_OR_REMOVE_CUSTOMER]));
    }

    #[Test]
    public function manager_cannot_edit_or_remove_other_customer(): void
    {
        $managerUser = $this->createManagerUser();
        $customerUser = $this->createCustomerUser();
        $managerToken = $this->createMockToken($managerUser);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($managerToken, $customerUser, [UserVoter::EDIT_OR_REMOVE_CUSTOMER]));
    }

    private function createManagerUser(): User
    {
        return new Manager('manager', 'manager@example.it');
    }

    private function createAdminUser(): User
    {
        return new Admin('admin', 'admin@example.it');
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
