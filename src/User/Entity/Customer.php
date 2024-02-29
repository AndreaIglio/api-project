<?php

declare(strict_types=1);

namespace App\User\Entity;

use App\User\Entity\Common\User;

final class Customer extends User
{
    private Manager $manager;

    public function __construct(
        Manager $manager,
        string $password,
        string $email,
        array $roles = [],
    ) {
        parent::__construct($password, $email, $roles);
        $this->manager = $manager;
    }

    public function getManager(): Manager
    {
        return $this->manager;
    }
}
