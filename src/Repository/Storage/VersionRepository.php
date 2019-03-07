<?php

namespace App\Repository\Storage;

use App\Entity\Storage\Storage;
use App\Entity\Storage\Version;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class VersionRepository
 *
 * @package App\Repository\Storage
 */
class VersionRepository extends ServiceEntityRepository
{
    /**
     * StorageVersionRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Version::class);
    }

    /**
     * @param Storage $storage
     *
     * @return Query
     */
    public function search(Storage $storage): Query
    {
        return $this->createQueryBuilder('t')
            ->select(['t', 'w', 'f'])
            ->leftJoin('t.file', 'f')
            ->leftJoin('f.workflow', 'w')
            ->where('t.storage = :storage')
            ->setParameters(['storage' => $storage])
            ->addOrderBy('w.updatedAt', 'DESC')
            ->getQuery();
    }

    /**
     * @param Storage $storage
     *
     * @return mixed
     */
    public function expireVersions(Storage $storage)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        return $queryBuilder->update('App:Storage\Version', 't')
            ->set('t.isActive', 0)
            ->andWhere($queryBuilder->expr()->eq('t.isActive', ':isActive'))
            ->andWhere($queryBuilder->expr()->eq('t.storage', ':storage'))
            ->setParameter(':isActive', 1)
            ->setParameter(':storage', $storage)
            ->getQuery()
            ->execute();
    }
}
