<?php

declare(strict_types=1);

namespace App\User\Controller;

use App\Common\Dto\JsonApiResponseDto;
use App\User\Entity\Manager;
use App\User\Hasher\UserPasswordHasherInterface;
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
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly ManagerRepositoryInterface $managerRepository,
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
            $createResult = $this->createNewManager($requestData);

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
            $updateResult = $this->updateManager($manager, $requestData);

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

    /**
     * @param array{
     *      email?: string|null,
     *      password?: string|null
     *  } $requestData
     *
     * @return array{'message': string}
     */
    private function createNewManager(array $requestData): array
    {
        $email = $requestData['email'] ?? null;
        $password = $requestData['password'] ?? null;

        if (!$email || !$password) {
            throw new \InvalidArgumentException('Email and password are needed.');
        }

        $manager = new Manager($password, $email);
        $this->userPasswordHasher->setHashedPassword($manager, $password);

        $this->managerRepository->add($manager);
        $this->managerRepository->flush();

        return ['message' => "Manager successfully created with email {$email} and Id {$manager->getId()}"];
    }

    /**
     * @param Manager $manager
     * @param array{
     *      email?: string|null,
     *      password?: string|null
     *  } $requestData
     *
     * @return array{'updated': bool, 'message': string}
     */
    private function updateManager(Manager $manager, array $requestData): array
    {
        $email = $requestData['email'] ?? null;
        $password = $requestData['password'] ?? null;
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
