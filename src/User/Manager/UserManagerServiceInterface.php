<?php

declare(strict_types=1);

namespace App\User\Manager;

use App\User\Dto\RequestUserDataDto;
use App\User\Entity\Customer;
use App\User\Entity\Manager;

interface UserManagerServiceInterface
{
    /**
     * @param Customer           $customer
     * @param RequestUserDataDto $requestUserDataDto
     *
     * @return array{'updated': bool, 'message': string}
     */
    public function updateCustomer(Customer $customer, RequestUserDataDto $requestUserDataDto): array;

    /**
     * @param Manager            $manager
     * @param RequestUserDataDto $requestUserDataDto
     *
     * @return array{'message': string}
     */
    public function createNewCustomer(Manager $manager, RequestUserDataDto $requestUserDataDto): array;

    /**
     * @param RequestUserDataDto $requestUserDataDto
     *
     * @return array{'message': string}
     */
    public function createNewManager(RequestUserDataDto $requestUserDataDto): array;

    /**
     * @param Manager            $manager
     * @param RequestUserDataDto $requestUserDataDto
     *
     * @return array{'updated': bool, 'message': string}
     */
    public function updateManager(Manager $manager, RequestUserDataDto $requestUserDataDto): array;
}
