<?php

declare(strict_types=1);

namespace App\MultimediaResource\Dto;

use App\MultimediaResource\Entity\MultimediaResource;

final readonly class MultimediaResourceResultDto
{
    public function __construct(
        private MultimediaResource $multimediaResource,
        private bool $isNew,
        private bool $isReloaded,
    ) {}

    public static function create(MultimediaResource $multimediaResource, bool $isNew, bool $isReloaded): self
    {
        return new self($multimediaResource, $isNew, $isReloaded);
    }

    public function getMultimediaResource(): MultimediaResource
    {
        return $this->multimediaResource;
    }

    public function isNew(): bool
    {
        return $this->isNew;
    }

    public function isReloaded(): bool
    {
        return $this->isReloaded;
    }
}
