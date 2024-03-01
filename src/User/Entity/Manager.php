<?php

declare(strict_types=1);

namespace App\User\Entity;

use App\User\Entity\Common\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Manager extends User
{
    /** @var Collection<array-key, Customer> */
    private Collection $customers;

    public function __construct(
        string $password,
        string $email
    ) {
        parent::__construct($password, $email, ['ROLE_MANAGER']);
        $this->customers = new ArrayCollection();
    }

    /** @return  Collection<array-key, Customer>  */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }
}
