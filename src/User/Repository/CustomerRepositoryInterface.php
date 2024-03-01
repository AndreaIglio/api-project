<?php

declare(strict_types=1);

namespace App\User\Repository;

use App\User\Entity\Customer;
use Symfony\Component\Uid\Uuid;

interface CustomerRepositoryInterface
{
    public function add(Customer $customer): void;

    public function remove(Customer $customer): void;

    public function findOneById(Uuid $id): ?Customer;

    public function flush(): void;
}
