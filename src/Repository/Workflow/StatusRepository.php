<?php

namespace App\Repository\Workflow;

use App\Entity\WorkflowStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class StatusRepository
 *
 * @package App\Repository\Workflow
 */
class StatusRepository extends ServiceEntityRepository
{
    /**
     * StatusRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WorkflowStatus::class);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function available(): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->addOrderBy('s.sort', 'ASC')
            ->addOrderBy('s.title', 'ASC');
    }

    /**
     * @return WorkflowStatus|null
     */
    public function getDefaultStatus(): ?WorkflowStatus
    {
        return $this->createQueryBuilder('s')
            ->where('s.isDefault = :isDefault')
            ->setParameters(['isDefault' => true])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
