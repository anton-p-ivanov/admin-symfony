<?php

namespace App\Service;

use App\Entity\Storage\Storage;
use App\Entity\Storage\Tree;
use App\Form\AccessType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

/**
 * Class AccessService
 * @package App\Service
 */
class AccessService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * DeleteEntity constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    public function apply(FormInterface $form): bool
    {
        $items = json_decode($form->get('items')->getNormData(), true);

        /* @var Tree[] $nodes */
        $nodes = $this->entityManager->getRepository(Tree::class)->findBy(['uuid' => $items]);
        if (!$nodes) {
            $form->addError(new FormError('form.errors.invalid_items'));
            return false;
        }

        foreach ($nodes as $node) {
            /* @var Storage[] $models */
            $models = [$node->getStorage()];

            if ($form->get('scope')->getNormData() === AccessType::SCOPE_CHILDREN) {
                $models = array_merge($models, $this->getDescendants($node));
            }

            /* @var ArrayCollection $roless */
            $roles = $form->get('roles')->getNormData();
            if ($roles->count() > 0) {
                foreach ($models as $model) {
                    $model->setRoles($roles);
                    $this->entityManager->persist($model);
                }
            }
        }

        $this->entityManager->flush();

        return true;
    }

    /**
     * @param Tree $child
     *
     * @return Storage
     */
    public function getStorage(Tree $child): Storage
    {
        return $child->getStorage();
    }

    /**
     * @param Tree $node
     *
     * @return array
     */
    protected function getDescendants(Tree $node): array
    {
        $descendants = $this->entityManager->createQueryBuilder()
            ->select(['t'])
            ->from(Tree::class, 't')
            ->andWhere('t.root = :root')
            ->andWhere('t.leftMargin > :leftMargin')
            ->andWhere('t.rightMargin < :rightMargin')
            ->setParameters([
                'root' => $node->getRoot(),
                'leftMargin' => $node->getLeftMargin(),
                'rightMargin' => $node->getRightMargin()
            ])
            ->getQuery()
            ->getResult();

        return array_map([$this, 'getStorage'], $descendants);
    }
}