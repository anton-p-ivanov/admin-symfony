<?php

namespace App\Listener\Workflow;

use App\Entity\Workflow;
use App\Entity\WorkflowStatus;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class WorkflowListener
 *
 * @package App\Listener\Workflow
 */
class WorkflowListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var mixed
     */
    private $user;

    /**
     * WorkflowListener constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Workflow $entity
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Workflow $entity, LifecycleEventArgs $event)
    {
        $token = $this->tokenStorage->getToken();
        if ($token && $this->user = $token->getUser()) {
            $entity->setCreated($this->user);
            $entity->setUpdated($this->user);
        }

        if ($entity->getStatus() === null) {
            $entity->setStatus($this->getDefaultStatus($event));
        }
    }

    /**
     * @param Workflow $entity
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(Workflow $entity, LifecycleEventArgs $event)
    {
        $entity->setUpdatedAt(new \DateTime());

        $token = $this->tokenStorage->getToken();
        if ($token && $this->user = $token->getUser()) {
            $entity->setUpdated($this->user);
        }

        if ($entity->getStatus() === null) {
            $entity->setStatus($this->getDefaultStatus($event));
        }
    }

    /**
     * @param LifecycleEventArgs $event
     * @return WorkflowStatus|null
     */
    protected function getDefaultStatus(LifecycleEventArgs $event)
    {
        return $event->getObjectManager()->getRepository(WorkflowStatus::class)->getDefaultStatus();
    }
}