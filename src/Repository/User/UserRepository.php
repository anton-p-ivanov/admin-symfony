<?php

namespace App\Repository\User;

use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class UserRepository
 * @package App\Repository\User
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param null|string $search
     *
     * @return Query
     */
    public function search(?string $search): Query
    {
        $builder = $this->createQueryBuilder('t')
            ->select(['t', 'w'])
            ->leftJoin('t.workflow', 'w')
            ->andWhere('w.uuid IS NULL OR w.isDeleted = :isDeleted')
            ->addOrderBy('CONCAT(t.fname, t.lname)', 'ASC')
            ->setParameter('isDeleted', false);

        if ($search) {
            $builder->andWhere('t.fname LIKE :search OR t.lname LIKE :search OR t.email LIKE :search')
                ->setParameter('search', "%$search%");
        }

        return $builder->getQuery();
    }

    /**
     * @param User $user
     * @param string $className
     *
     * @return mixed
     */
    public function expireEntities(User $user, string $className)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        return $queryBuilder->update($className, 'c')
            ->set('c.isExpired', true)
            ->where('c.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
