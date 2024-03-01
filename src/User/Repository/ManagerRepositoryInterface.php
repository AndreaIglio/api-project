<?php

declare(strict_types=1);

namespace App\User\Repository;

use App\User\Entity\Manager;
use Symfony\Component\Uid\Uuid;

interface ManagerRepositoryInterface
{
    public function add(Manager $manager): void;

    public function remove(Manager $manager): void;

    public function findOneById(Uuid $id): ?Manager;

    public function findOneByEmail(string $email): ?Manager;

    public function flush(): void;
}
