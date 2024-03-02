<?php

declare(strict_types=1);

namespace App\MultimediaResource\Repository;

use App\MultimediaResource\Entity\MultimediaResource;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

interface MultimediaResourceRepositoryInterface
{
    public function add(MultimediaResource $multimediaResource): void;

    public function findOneByFileName(string $fileName): ?MultimediaResource;

    public function findOneById(Uuid $id): ?MultimediaResource;

    public function remove(MultimediaResource $multimediaResource): void;

    /**
     * @return Collection<array-key, MultimediaResource>
     */
    public function findAllMultimediaResources(): Collection;

    /**
     * @return Collection<array-key, MultimediaResource>
     */
    public function findByCustomerId(Uuid $customerId): Collection;

    /**
     * @return Collection<array-key, MultimediaResource>
     */
    public function findByManagerId(Uuid $managerId): Collection;

    public function flush(): void;
}
