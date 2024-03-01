<?php

declare(strict_types=1);

namespace App\User\Hasher;

use App\User\Entity\Common\UserInterface;

interface UserPasswordHasherInterface
{
    public function setHashedPassword(UserInterface $user, string $plainPassword): void;
}
