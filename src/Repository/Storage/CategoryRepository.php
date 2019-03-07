<?php

namespace App\Repository\Storage;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class CategoryRepository
 *
 * @package App\Repository\Storage
 */
class CategoryRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function available(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.workflow', 'w')
            ->andWhere('c.isActive = :isActive')
            ->andWhere('w.uuid IS NULL OR w.isDeleted = :isDeleted')
            ->addOrderBy('c.sort', 'ASC')
            ->addOrderBy('c.title', 'ASC')
            ->setParameters([
                'isActive' => true,
                'isDeleted' => false,
            ]);
    }
}
