<?php

declare(strict_types=1);

namespace App\User\Entity;

use App\User\Entity\Common\User;

final class Admin extends User
{
    public function __construct(
        string $password,
        string $email
    ) {
        parent::__construct($password, $email, ['ROLE_ADMIN']);
    }
}
