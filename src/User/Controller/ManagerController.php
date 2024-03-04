<?php

declare(strict_types=1);

namespace App\User\Controller;

use App\Common\Dto\JsonApiResponseDto;
use App\User\Dto\RequestUserDataDto;
use App\User\Entity\Manager;
use App\User\Manager\UserManagerServiceInterface;
use App\User\Repository\ManagerRepositoryInterface;
use App\User\Voter\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

final class ManagerController extends AbstractController
{
    public function __construct(
        private readonly ManagerRepositoryInterface $managerRepository,
        private readonly UserManagerServiceInterface $userManagerService,
    ) {}

    /**
     * @Route("/api/manager/add", name="manager_add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::CREATE_MANAGER);
        $requestData = json_decode($request->getContent(), true) ?: [];
        Assert::isArray($requestData);

        try {
            /**
             * @var array{email: ?string, password: ?string} $requestData
             */
            $createResult = $this->userManagerService->createNewManager(
                RequestUserDataDto::fromRequestData($requestData)
            );

            return JsonApiResponseDto::success($createResult['message']);
        } catch (\Exception $e) {
            return JsonApiResponseDto::error($e->getMessage());
        }
    }

    /**
     * @Route("/api/manager/{managerId}/edit", name="manager_edit", methods={"PUT"})
     */
    public function edit(Request $request, string $managerId): Response
    {
        if (!Uuid::isValid($managerId)) {
            throw $this->createNotFoundException(sprintf('The manager ID "%s" is not valid!', $managerId));
        }

        $manager = $this->managerRepository->findOneById(Uuid::fromString($managerId));

        if (!$manager) {
            throw $this->createNotFoundException(sprintf('Manager with ID "%s" not found.', $managerId));
        }

        $this->denyAccessUnlessGranted(UserVoter::EDIT_OR_REMOVE_MANAGER, $manager);

        $requestData = json_decode($request->getContent(), true) ?: [];
        Assert::isArray($requestData);

        try {
            /**
             * @var array{email: ?string, password: ?string} $requestData
             */
            $updateResult = $this->userManagerService->updateManager($manager, RequestUserDataDto::fromRequestData($requestData));

            return JsonApiResponseDto::success($updateResult['message']);
        } catch (\Exception $e) {
            return JsonApiResponseDto::error($e->getMessage());
        }
    }

    /**
     * @Route("/api/manager/{managerId}/remove", name="manager_remove", methods={"DELETE"})
     */
    public function remove(Request $request, string $managerId): Response
    {
        if (!Uuid::isValid($managerId)) {
            throw $this->createNotFoundException(sprintf('The manager ID "%s" is not valid!', $managerId));
        }

        $manager = $this->managerRepository->findOneById(Uuid::fromString($managerId));

        if (!$manager) {
            throw $this->createNotFoundException(sprintf('Manager with ID "%s" not found.', $managerId));
        }

        $this->denyAccessUnlessGranted(UserVoter::EDIT_OR_REMOVE_MANAGER, $manager);

        try {
            $this->managerRepository->remove($manager);
            $this->managerRepository->flush();

            return JsonApiResponseDto::success('Manager successfully removed.');
        } catch (\Exception $e) {
            return JsonApiResponseDto::error($e->getMessage());
        }
    }
}
