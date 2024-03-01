<?php

declare(strict_types=1);

namespace App\User\Hasher;

use App\User\Entity\Common\User;
use App\User\Entity\Common\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as SymfonyUserPasswordHasherInterface;
use Webmozart\Assert\Assert;

final class UserPasswordHasher implements UserPasswordHasherInterface
{
    public function __construct(
        private SymfonyUserPasswordHasherInterface $symfonyUserPasswordHasher,
    ) {}

    public function setHashedPassword(UserInterface $user, string $plainPassword): void
    {
        Assert::isInstanceOf($user, User::class);

        $user->setPassword($this->symfonyUserPasswordHasher->hashPassword($user, $plainPassword));
    }
}
