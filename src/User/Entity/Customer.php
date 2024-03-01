<?php

declare(strict_types=1);

namespace App\User\Entity;

use App\MultimediaResource\Entity\MultimediaResource;
use App\User\Entity\Common\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Customer extends User
{
    private Manager $manager;

    /** @var Collection<array-key, MultimediaResource> */
    private Collection $multimediaResources;

    public function __construct(
        Manager $manager,
        string $password,
        string $email,
    ) {
        parent::__construct($password, $email, ['ROLE_CUSTOMER']);
        $this->manager = $manager;
        $this->multimediaResources = new ArrayCollection();
    }

    public function getManager(): Manager
    {
        return $this->manager;
    }

    /** @return  Collection<array-key, MultimediaResource>  */
    public function getMultimediaResources(): Collection
    {
        return $this->multimediaResources;
    }
}
