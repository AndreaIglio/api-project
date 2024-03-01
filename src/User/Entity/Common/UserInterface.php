<?php

declare(strict_types=1);

namespace App\User\Entity\Common;

use Symfony\Component\Uid\Uuid;

interface UserInterface
{
    public function getId(): Uuid;

    public function getEmail(): string;

    public function setEmail(string $email): void;
}
