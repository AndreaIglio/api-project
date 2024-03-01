<?php

declare(strict_types=1);

namespace App\User\Controller;

use App\Common\Dto\JsonApiResponseDto;
use App\User\Entity\Customer;
use App\User\Entity\Manager;
use App\User\Hasher\UserPasswordHasherInterface;
use App\User\Repository\CustomerRepositoryInterface;
use App\User\Repository\ManagerRepositoryInterface;
use App\User\Voter\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

final class CustomerController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly ManagerRepositoryInterface $managerRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
    ) {}

    /**
     * @Route("/api/customer/add", name="customer_add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::CREATE_CUSTOMER);
        $requestData = json_decode($request->getContent(), true) ?: [];
        Assert::isArray($requestData);

        try {
            $user = $this->getUser();
            Assert::notNull($user);
            $manager = $this->managerRepository->findOneByEmail($user->getUserIdentifier());

            if (!$manager) {
                throw $this->createNotFoundException('Manager not found.');
            }
            $createResult = $this->createNewCustomer($manager, $requestData);

            return JsonApiResponseDto::success($createResult['message']);
        } catch (\Exception $e) {
            return JsonApiResponseDto::error($e->getMessage());
        }
    }

    /**
     * @Route("/api/customer/{customerId}/edit", name="customer_edit", methods={"PUT"})
     */
    public function edit(Request $request, string $customerId): Response
    {
        if (!Uuid::isValid($customerId)) {
            throw $this->createNotFoundException(sprintf('The customer ID "%s" is not valid!', $customerId));
        }

        $customer = $this->customerRepository->findOneById(Uuid::fromString($customerId));

        if (!$customer) {
            throw $this->createNotFoundException(sprintf('Customer with ID "%s" not found.', $customerId));
        }

        $this->denyAccessUnlessGranted(UserVoter::EDIT_OR_REMOVE_CUSTOMER, $customer);

        $requestData = json_decode($request->getContent(), true) ?: [];
        Assert::isArray($requestData);

        try {
            $updateResult = $this->updateCustomer($customer, $requestData);

            return JsonApiResponseDto::success($updateResult['message']);
        } catch (\Exception $e) {
            return JsonApiResponseDto::error($e->getMessage());
        }
    }

    /**
     * @Route("/api/customer/{customerId}/remove", name="customer_remove", methods={"DELETE"})
     */
    public function remove(Request $request, string $customerId): Response
    {
        if (!Uuid::isValid($customerId)) {
            throw $this->createNotFoundException(sprintf('The customer ID "%s" is not valid!', $customerId));
        }

        $customer = $this->customerRepository->findOneById(Uuid::fromString($customerId));

        if (!$customer) {
            throw $this->createNotFoundException(sprintf('Customer with ID "%s" not found.', $customerId));
        }

        $this->denyAccessUnlessGranted(UserVoter::EDIT_OR_REMOVE_CUSTOMER, $customer);

        try {
            $this->customerRepository->remove($customer);
            $this->customerRepository->flush();

            return JsonApiResponseDto::success('Customer successfully removed.');
        } catch (\Exception $e) {
            return JsonApiResponseDto::error($e->getMessage());
        }
    }

    /**
     * @param Customer $customer
     * @param array{
     *      email?: string|null,
     *      password?: string|null
     *  } $requestData
     *
     * @return array{'updated': bool, 'message': string}
     */
    private function updateCustomer(Customer $customer, array $requestData): array
    {
        $email = $requestData['email'] ?? null;
        $password = $requestData['password'] ?? null;
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

    /**
     * @param Manager $manager
     * @param array{
     *      email?: string|null,
     *      password?: string|null
     *  } $requestData
     *
     * @return array{'message': string}
     */
    private function createNewCustomer(Manager $manager, array $requestData): array
    {
        $email = $requestData['email'] ?? null;
        $password = $requestData['password'] ?? null;

        if (!$email || !$password) {
            throw new \InvalidArgumentException('Email and password are needed.');
        }

        $customer = new Customer($manager, $password, $email);
        $this->userPasswordHasher->setHashedPassword($customer, $password);

        $this->customerRepository->add($customer);
        $this->customerRepository->flush();

        return ['message' => "Customer successfully created with email {$email} and Id {$customer->getId()} related with Manager {$manager->getEmail()}"];
    }
}
