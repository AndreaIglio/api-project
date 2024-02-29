<?php

declare(strict_types=1);

namespace App\User\Entity;

use App\Common\Trait\UuidTrait;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use UuidTrait;

    /**
     * @param string[] $roles
     */
    public function __construct(
        protected string $username,
        protected string $password,
        protected string $email,
        protected array $roles = [],
    ) {
        $this->initializeUuid();
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $newPassword): void
    {
        $this->password = $newPassword;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function eraseCredentials(): void {}
}
