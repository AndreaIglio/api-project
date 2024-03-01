<?php

declare(strict_types=1);

namespace App\User\Repository;

use App\User\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/** @extends ServiceEntityRepository<Customer> */
final class CustomerRepository extends ServiceEntityRepository implements CustomerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function add(Customer $customer): void
    {
        $this->getEntityManager()->persist($customer);
    }

    public function remove(Customer $customer): void
    {
        $this->getEntityManager()->remove($customer);
    }

    public function findOneById(Uuid $id): ?Customer
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
