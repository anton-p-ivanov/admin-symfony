<?php

namespace App\Repository\Storage;

use App\Entity\Storage\Storage;
use App\Entity\Storage\Tree;
//use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TreeRepository
 *
 * @package App\Repository\Storage
 */
class TreeRepository extends NestedTreeRepository
{
    /**
     * @param Tree $parentNode
     * @param Request $request
     *
     * @return \Doctrine\ORM\Query
     */
    public function search(Tree $parentNode, Request $request): Query
    {
        $queryBuilder = $this->getChildrenQueryBuilder($parentNode, true);

        $conditions = $queryBuilder->expr()->andX();

        if ($search = $request->get('search')) {
            $queryBuilder = $this->createQueryBuilder('node');
            $conditions->add('s.type = :type');
            $conditions->add('s.title LIKE :search OR s.description LIKE :search OR f.name LIKE :search');
            $queryBuilder->setParameter('type', Storage::STORAGE_TYPE_FILE);
            $queryBuilder->setParameter('search', "%$search%");
        }

        $conditions->add('w.uuid IS NULL OR w.isDeleted = :isDeleted');
        $queryBuilder->setParameter('isDeleted', false);

        $queryBuilder->orderBy('s.type', 'ASC');
        if ($sort = $request->get('sort')) {
            $sortOrder = 'ASC';
            $sortBy = $sort;
            if (strpos($sort, '-') === 0) {
                $sortOrder = 'DESC';
                $sortBy = substr($sort, 1);
            }

            $queryBuilder->addOrderBy('s.'. $sortBy, $sortOrder);
        }
        else {
            $queryBuilder->addOrderBy('s.title', 'ASC');
        }

        return $queryBuilder
            ->addSelect(['s', 'w', 'v', 'f'])
            ->innerJoin('node.storage', 's')
            ->leftJoin('s.versions', 'v')
            ->leftJoin('v.file', 'f')
            ->leftJoin('s.workflow', 'w')
            ->andWhere($conditions)
            ->getQuery();
    }

    /**
     * @return QueryBuilder
     */
    public function getTree(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->select(['t', 's'])
            ->leftJoin('t.storage', 's')
            ->andWhere('s.uuid IS NULL OR s.type = :type')
            ->addOrderBy('t.root', 'ASC')
            ->addOrderBy('t.leftMargin', 'ASC')
            ->setParameters([
                'type' => Storage::STORAGE_TYPE_DIR
            ]);
    }

    /**
     * @param Storage $element
     */
    public function setTreeNode(Storage $element): void
    {
        $isNewElement = $element->getUuid() === null;
        $this->getEntityManager()->persist($element);

        if (!$isNewElement) {
            if ($this->isNodeChanged($element)) {
                $this->updateNodes($element);
            }
        }
        else {
            $this->insertNodes($element);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param Storage $element
     *
     * @return bool
     */
    private function isNodeChanged(Storage $element): bool
    {
        return $this->findOneBy(['storage' => $element]) !== $element->getParent();
    }

    /**
     * @param Storage $element
     */
    private function insertNodes(Storage $element): void
    {
        $node = new Tree();
        $node->setStorage($element);
        $node->setParent($element->getNode()->getParent());

        $this->getEntityManager()->persist($node);
    }

    /**
     * @param Storage $element
     */
    private function updateNodes(Storage $element): void
    {
//        if ($element->isDirectory()) {
            if ($parent = $element->getParent()) {
                $this->persistAsFirstChildOf($element->getNode(), $parent);
            }
//        }
//        else {
//            $previousNodes = $this->findBy(['storage' => $element]);
//            if ($previousNodes) {
//                foreach ($previousNodes as $node) {
//                    $this->removeFromTree($node);
//                }
//            }
//
//            $this->insertNodes($element);
//        }
    }
}
