<?php

declare(strict_types=1);

namespace App\MultimediaResource\Entity;

use App\Common\Trait\UuidTrait;
use App\User\Entity\Customer;

final class MultimediaResource
{
    use UuidTrait;

    private string $fileName;

    private string $ext;

    public function __construct(
        private readonly Customer $customer,
    ) {}
}
