<?php

declare(strict_types=1);

namespace App\MultimediaResource\Manager;

use App\MultimediaResource\Dto\MultimediaResourceResultDto;
use App\MultimediaResource\Entity\MultimediaResource;
use App\MultimediaResource\Repository\MultimediaResourceRepositoryInterface;
use App\User\Entity\Customer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\Assert\Assert;

final readonly class MultimediaResourceManager
{
    public function __construct(
        private MultimediaResourceRepositoryInterface $multimediaResourceRepository,
        private string $uploadDirectory,
    ) {}

    public function addAndMoveFileToDirectory(Customer $user, UploadedFile $file): MultimediaResourceResultDto
    {
        $fileContent = file_get_contents($file->getPathname());
        Assert::string($fileContent);
        $hash = sha1($fileContent);

        $extension = $file->guessExtension();
        Assert::string($extension);

        $fileName = $hash;
        $filePath = $this->uploadDirectory.'/'.$fileName.$extension;

        $existingResource = $this->multimediaResourceRepository->findOneByFileName($fileName);

        if ($existingResource) {
            if (!file_exists($filePath)) {
                // File not existing in the directory, move it without creating new db value
                $file->move($this->uploadDirectory, $fileName.'.'.$extension);

                return MultimediaResourceResultDto::create($existingResource, false, true);
            }

            // File exist in db and in directory so return
            return MultimediaResourceResultDto::create($existingResource, false, false);
        }
        // if resource doesn't exist create new one
        $multimediaResource = new MultimediaResource($user);
        $multimediaResource->setFileName($fileName);
        $multimediaResource->setExt($extension);
        $file->move($this->uploadDirectory, $fileName.'.'.$extension);
        $this->multimediaResourceRepository->add($multimediaResource);
        $this->multimediaResourceRepository->flush();

        return MultimediaResourceResultDto::create($multimediaResource, true, false);
    }

    public function removeResource(MultimediaResource $resource): void
    {
        $filePath = $this->uploadDirectory.'/'.$resource->getFileName(). '.' .$resource->getExt();
        $fileSystem = new Filesystem();

        if ($fileSystem->exists($filePath)) {
            $fileSystem->remove($filePath);
        }

        $this->multimediaResourceRepository->remove($resource);
        $this->multimediaResourceRepository->flush();
    }

    public function updateResource(MultimediaResource $multimediaResource, string $newFileName, string $newExtension): MultimediaResource
    {
        $oldFilePath = $this->uploadDirectory.'/'.$multimediaResource->getFileName().'.'.$multimediaResource->getExt();
        $newFilePath = $this->uploadDirectory.'/'.$newFileName.'.'.$newExtension;

        $fileSystem = new Filesystem();

        if ($fileSystem->exists($oldFilePath)) {
            $fileSystem->rename($oldFilePath, $newFilePath);
        }

        $multimediaResource->setFileName($newFileName);
        $multimediaResource->setExt($newExtension);
        $this->multimediaResourceRepository->add($multimediaResource);
        $this->multimediaResourceRepository->flush();

        return $multimediaResource;
    }
}
