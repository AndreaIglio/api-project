<?php

declare(strict_types=1);

namespace App\User\Dto;

final readonly class RequestUserDataDto
{
    public function __construct(
        private ?string $email,
        private ?string $password
    ) {}

    /**
     * @param array{email: ?string, password: ?string} $requestData
     */
    public static function fromRequestData(array $requestData): self
    {
        return new self(
            email: $requestData['email'] ?? null,
            password: $requestData['password'] ?? null
        );
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
}
