<?php

namespace App\Listener\Workflow;

use App\Entity\User\User;
use App\Entity\Workflow;
use App\Entity\WorkflowStatus;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * WorkflowListener constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $manager
     */
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $manager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->manager = $manager;
    }

    /**
     * @param Workflow $entity
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Workflow $entity, LifecycleEventArgs $event)
    {
        if ($user = $this->getUser()) {
            $entity->setCreated($user);
            $entity->setUpdated($user);
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

        if ($user = $this->getUser()) {
            $entity->setUpdated($user);
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

    /**
     * @return User|null
     */
    protected function getUser(): ?User
    {
        $token = $this->tokenStorage->getToken();

        if ($token && $user = $token->getUser()) {
            return $this->manager->getRepository(User::class)->findOneBy(['email' => $user->getUsername()]);
        }

        return null;
    }
}