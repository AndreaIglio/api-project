<?php

declare(strict_types=1);

namespace App\User\Manager;

use App\User\Dto\RequestUserDataDto;
use App\User\Entity\Customer;
use App\User\Entity\Manager;
use App\User\Hasher\UserPasswordHasherInterface;
use App\User\Repository\CustomerRepositoryInterface;
use App\User\Repository\ManagerRepositoryInterface;

final readonly class UserManagerService implements UserManagerServiceInterface
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private ManagerRepositoryInterface $managerRepository,
        private CustomerRepositoryInterface $customerRepository,
    ) {}

    public function updateCustomer(Customer $customer, RequestUserDataDto $requestUserDataDto): array
    {
        $email = $requestUserDataDto->getEmail() ?? null;
        $password = $requestUserDataDto->getPassword() ?? null;
        $isUpdated = false;
        $updateMessage = 'Customer successfully updated';

        if ($email && $customer->getEmail() !== $email) {
            $customer->setEmail($email);
            $isUpdated = true;
            $updateMessage .= " with email {$email}";
        }

        if ($password) {
            $this->userPasswordHasher->setHashedPassword($customer, $password);
            $isUpdated = true;
            $updateMessage .= $email ? ' and new password' : ' with also new password';
        }

        if ($isUpdated) {
            $this->customerRepository->add($customer);
            $this->customerRepository->flush();
        }

        return [
            'updated' => $isUpdated,
            'message' => $updateMessage,
        ];
    }

    public function createNewCustomer(Manager $manager, RequestUserDataDto $requestUserDataDto): array
    {
        $email = $requestUserDataDto->getEmail() ?? null;
        $password = $requestUserDataDto->getPassword() ?? null;

        if (!$email || !$password) {
            throw new \InvalidArgumentException('Email and password are needed.');
        }

        $customer = new Customer($manager, $password, $email);
        $this->userPasswordHasher->setHashedPassword($customer, $password);

        $this->customerRepository->add($customer);
        $this->customerRepository->flush();

        return ['message' => "Customer successfully created with email {$email} and Id {$customer->getId()} related with Manager {$manager->getEmail()}"];
    }

    public function createNewManager(RequestUserDataDto $requestUserDataDto): array
    {
        $email = $requestUserDataDto->getEmail() ?? null;
        $password = $requestUserDataDto->getPassword() ?? null;

        if (!$email || !$password) {
            throw new \InvalidArgumentException('Email and password are needed.');
        }

        $manager = new Manager($password, $email);
        $this->userPasswordHasher->setHashedPassword($manager, $password);

        $this->managerRepository->add($manager);
        $this->managerRepository->flush();

        return ['message' => "Manager successfully created with email {$email} and Id {$manager->getId()}"];
    }

    public function updateManager(Manager $manager, RequestUserDataDto $requestUserDataDto): array
    {
        $email = $requestUserDataDto->getEmail() ?? null;
        $password = $requestUserDataDto->getPassword() ?? null;
        $isUpdated = false;
        $updateMessage = 'Manager successfully updated';

        if ($email && $manager->getEmail() !== $email) {
            $manager->setEmail($email);
            $isUpdated = true;
            $updateMessage .= " with email {$email}";
        }

        if ($password) {
            $this->userPasswordHasher->setHashedPassword($manager, $password);
            $isUpdated = true;
            $updateMessage .= $email ? ' and new password' : ' with also new password';
        }

        if ($isUpdated) {
            $this->managerRepository->add($manager);
            $this->managerRepository->flush();
        }

        return [
            'updated' => $isUpdated,
            'message' => $updateMessage,
        ];
    }
}
