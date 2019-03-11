<?php

namespace App\Service;

use App\Entity\Workflow;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DeleteService
 * @package App\Service
 */
class DeleteService
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
     * @param mixed $entity
     *
     * @return bool
     */
    public function delete($entity): bool
    {
        $workflow = null;

        if (method_exists($entity, 'getWorkflow')) {
            $workflow = $entity->getWorkflow();
        }

        if ($workflow instanceof Workflow) {
            $workflow->setIsDeleted(true);
            $this->entityManager->persist($workflow);
        }
        else {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();

        return true;
    }
}