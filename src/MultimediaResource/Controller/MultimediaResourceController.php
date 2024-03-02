<?php

declare(strict_types=1);

namespace App\MultimediaResource\Controller;

use App\Common\Dto\JsonApiResponseDto;
use App\MultimediaResource\Manager\MultimediaResourceManager;
use App\MultimediaResource\Repository\MultimediaResourceRepositoryInterface;
use App\MultimediaResource\Voter\MultimediaResourceVoter;
use App\User\Entity\Admin;
use App\User\Entity\Customer;
use App\User\Entity\Manager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

final class MultimediaResourceController extends AbstractController
{
    public function __construct(
        private readonly MultimediaResourceRepositoryInterface $multimediaResourceRepository,
        private readonly MultimediaResourceManager $multimediaResourceManager,
    ) {}

    /**
     * @Route("/api/multimedia-resource/add", name="multimedia_resource_add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $this->denyAccessUnlessGranted(MultimediaResourceVoter::CREATE);

        $user = $this->getUser();
        Assert::isInstanceOf($user, Customer::class);

        $file = $request->files->get('file');

        if (!$file) {
            return JsonApiResponseDto::error('No file provided.');
        }
        Assert::isInstanceOf($file, UploadedFile::class);
        $resultDto = $this->multimediaResourceManager->addAndMoveFileToDirectory($user, $file);

        if ($resultDto->isNew()) {
            return JsonApiResponseDto::success([
                'message' => 'Resource added successfully.',
                'id' => $resultDto->getMultimediaResource()->getId(),
                'fileName' => $resultDto->getMultimediaResource()->getFileName(),
            ]);
        }

        if ($resultDto->isReloaded()) {
            return JsonApiResponseDto::success([
                'message' => 'File reuploaded successfully.',
                'id' => $resultDto->getMultimediaResource()->getId(),
                'fileName' => $resultDto->getMultimediaResource()->getFileName(),
            ]);
        }

        return JsonApiResponseDto::error('Resource already exists.');
    }

    /**
     * @Route("/api/multimedia-resource/{multimediaResourceId}/remove", name="multimedia_resource_remove", methods={"DELETE"})
     */
    public function remove(Request $request, string $multimediaResourceId): Response
    {
        if (!Uuid::isValid($multimediaResourceId)) {
            throw $this->createNotFoundException(sprintf('The multimedia resource ID "%s" is not valid!', $multimediaResourceId));
        }

        $multimediaResource = $this->multimediaResourceRepository->findOneById(Uuid::fromString($multimediaResourceId));

        if (!$multimediaResource) {
            throw $this->createNotFoundException(sprintf('Multimedia resource with ID "%s" not found.', $multimediaResourceId));
        }

        $this->denyAccessUnlessGranted(MultimediaResourceVoter::EDIT_OR_REMOVE, $multimediaResource);

        try {
            $this->multimediaResourceManager->removeResource($multimediaResource);

            return JsonApiResponseDto::success(['message' => 'Resource deleted successfully.']);
        } catch (\Exception $e) {
            return JsonApiResponseDto::error($e->getMessage());
        }
    }

    /**
     * @Route("/api/multimedia-resource/{multimediaResourceId}/edit", name="multimedia_resource_edit", methods={"PUT"})
     */
    public function edit(Request $request, string $multimediaResourceId): Response
    {
        if (!Uuid::isValid($multimediaResourceId)) {
            throw $this->createNotFoundException(sprintf('The multimedia resource ID "%s" is not valid!', $multimediaResourceId));
        }

        $multimediaResource = $this->multimediaResourceRepository->findOneById(Uuid::fromString($multimediaResourceId));

        if (!$multimediaResource) {
            throw $this->createNotFoundException(sprintf('Multimedia resource with ID "%s" not found.', $multimediaResourceId));
        }

        $this->denyAccessUnlessGranted(MultimediaResourceVoter::EDIT_OR_REMOVE, $multimediaResource);

        $requestData = json_decode($request->getContent(), true) ?: [];
        Assert::isArray($requestData);

        $newFileName = $requestData['fileName'] ?? null;
        $newExtension = $requestData['ext'] ?? null;

        if (!is_string($newFileName) || !is_string($newExtension)) {
            return JsonApiResponseDto::error('New file name and extension are required and need to be string values.');
        }

        try {
            $updatedResource = $this->multimediaResourceManager->updateResource($multimediaResource, $newFileName, $newExtension);

            return JsonApiResponseDto::success([
                'message' => 'Resource updated successfully.',
                'id' => $updatedResource->getId(),
                'fileName' => $updatedResource->getFileName(),
                'ext' => $updatedResource->getExt(),
            ]);
        } catch (\Exception $e) {
            return JsonApiResponseDto::error($e->getMessage());
        }
    }

    /**
     * @Route("/api/multimedia-resource/show", name="multimedia_resource_show", methods={"GET"})
     */
    public function show(Request $request): Response
    {
        $this->denyAccessUnlessGranted(MultimediaResourceVoter::VIEW);
        $user = $this->getUser();
        Assert::notNull($user);

        $multimediaResources = new ArrayCollection();

        if ($user instanceof Admin) {
            $multimediaResources = $this->multimediaResourceRepository->findAllMultimediaResources();
        }

        if ($user instanceof Manager) {
            $multimediaResources = $this->multimediaResourceRepository->findByManagerId($user->getId());
        }

        if ($user instanceof Customer) {
            $multimediaResources = $this->multimediaResourceRepository->findByCustomerId($user->getId());
        }

        $multimediaResourcesArray = [];

        foreach ($multimediaResources as $multimediaResource) {
            $multimediaResourcesArray[] = $multimediaResource->toArray();
        }

        return JsonApiResponseDto::success(
            [
                'multimediaResources' => $multimediaResourcesArray,
            ],
            'Resources retrieved successfully'
        );
    }
}
