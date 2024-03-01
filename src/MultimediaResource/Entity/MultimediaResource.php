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
    ) {
        $this->initializeUuid();
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function setExt(string $ext): void
    {
        $this->ext = $ext;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getExt(): string
    {
        return $this->ext;
    }
}
