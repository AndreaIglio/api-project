<?php

declare(strict_types=1);

namespace App\User\Entity;

use App\User\Entity\Common\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class Manager extends User
{
    private Collection $customers;

    public function __construct(
        string $password,
        string $email,
        array $roles = []
    ) {
        parent::__construct($password, $email, $roles);
        $this->customers = new ArrayCollection();
    }

    public function getCustomers(): Collection
    {
        return $this->customers;
    }
}
