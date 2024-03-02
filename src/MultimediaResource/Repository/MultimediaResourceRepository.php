<?php

declare(strict_types=1);

namespace App\MultimediaResource\Repository;

use App\MultimediaResource\Entity\MultimediaResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/** @extends ServiceEntityRepository<MultimediaResource> */
final class MultimediaResourceRepository extends ServiceEntityRepository implements MultimediaResourceRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MultimediaResource::class);
    }

    public function add(MultimediaResource $multimediaResource): void
    {
        $this->getEntityManager()->persist($multimediaResource);
    }

    public function findOneById(Uuid $id): ?MultimediaResource
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findOneByFileName(string $fileName): ?MultimediaResource
    {
        return $this->findOneBy(['fileName' => $fileName]);
    }

    public function remove(MultimediaResource $multimediaResource): void
    {
        $this->getEntityManager()->remove($multimediaResource);
    }

    /**
     * @return Collection<array-key, MultimediaResource>
     */
    public function findAllMultimediaResources(): Collection
    {
        return new ArrayCollection(parent::findAll());
    }

    /**
     * @return Collection<array-key, MultimediaResource>
     */
    public function findByCustomerId(Uuid $customerId): Collection
    {
        return new ArrayCollection($this->findBy(['customer_id' => $customerId]));
    }

    /**
     * @return Collection<array-key, MultimediaResource>
     */
    public function findByManagerId(Uuid $managerId): Collection
    {
        $qb = $this->createQueryBuilder('m')
            ->innerJoin('m.customer', 'c')
            ->where('c.manager = :managerId')
            ->setParameter('managerId', $managerId);

        /** @var array<array-key, MultimediaResource> $result */
        $result = $qb->getQuery()->getResult();

        return new ArrayCollection($result);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
