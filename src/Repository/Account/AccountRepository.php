<?php

namespace App\Repository\Account;

use App\Entity\Account\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class AccountRepository
 *
 * @package App\Repository\Account
 */
class AccountRepository extends ServiceEntityRepository
{
    /**
     * AccountRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @return QueryBuilder
     */
    public function available(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->innerJoin('t.workflow', 'w')
            ->andWhere('t.isActive = :isActive')
            ->andWhere('w.uuid IS NULL OR w.isDeleted = :isDeleted')
            ->setParameters([
                'isActive' => true,
                'isDeleted' => false
            ])
            ->addOrderBy('t.sort', 'ASC')
            ->addOrderBy('t.title', 'ASC');
    }
}
