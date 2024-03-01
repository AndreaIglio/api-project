<?php

declare(strict_types=1);

namespace App\Common\Trait;

use Symfony\Component\Uid\Uuid;

trait UuidTrait
{
    private Uuid $id;

    public function getId(): Uuid
    {
        return $this->id;
    }

    private function initializeUuid(): void
    {
        $this->id = Uuid::v6();
    }
}
