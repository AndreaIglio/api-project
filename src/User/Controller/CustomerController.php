<?php

declare(strict_types=1);

namespace App\User\Controller;

use App\Common\Dto\JsonApiResponseDto;
use App\User\Dto\RequestUserDataDto;
use App\User\Entity\Customer;
use App\User\Manager\UserManagerServiceInterface;
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
        private readonly ManagerRepositoryInterface $managerRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly UserManagerServiceInterface $userManagerService,
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
            /**
             * @var array{email: ?string, password: ?string} $requestData
             */
            $createResult = $this->userManagerService->createNewCustomer($manager, RequestUserDataDto::fromRequestData($requestData));

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
            /**
             * @var array{email: ?string, password: ?string} $requestData
             */
            $updateResult = $this->userManagerService->updateCustomer($customer, RequestUserDataDto::fromRequestData($requestData));

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
}
