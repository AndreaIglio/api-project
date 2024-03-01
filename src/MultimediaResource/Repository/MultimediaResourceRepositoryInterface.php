<?php

declare(strict_types=1);

namespace App\MultimediaResource\Repository;

use App\MultimediaResource\Entity\MultimediaResource;
use Symfony\Component\Uid\Uuid;

interface MultimediaResourceRepositoryInterface
{
    public function add(MultimediaResource $multimediaResource): void;

    public function findOneByFileName(string $fileName): ?MultimediaResource;

    public function findOneById(Uuid $id): ?MultimediaResource;

    public function remove(MultimediaResource $multimediaResource): void;

    public function flush(): void;
}
