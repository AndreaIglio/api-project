<?php

declare(strict_types=1);

namespace App\User\Repository;

use App\User\Entity\Manager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/** @extends ServiceEntityRepository<Manager> */
final class ManagerRepository extends ServiceEntityRepository implements ManagerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Manager::class);
    }

    public function add(Manager $manager): void
    {
        $this->getEntityManager()->persist($manager);
    }

    public function remove(Manager $manager): void
    {
        $this->getEntityManager()->remove($manager);
    }

    public function findOneById(Uuid $id): ?Manager
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findOneByEmail(string $email): ?Manager
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
