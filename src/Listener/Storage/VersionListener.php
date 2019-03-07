<?php

namespace App\Listener\Storage;

use App\Entity\Storage\Storage;
use App\Entity\Storage\Version;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\PreUpdateEventArgs;

/**
 * Class VersionListener
 *
 * @package App\Listener\Storage
 */
class VersionListener
{
    /**
     * @param Version $version
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Version $version, LifecycleEventArgs $event)
    {
        if ($version->isActive()) {
            $this->expireVersions($event->getObjectManager(), $version->getStorage());
        }
    }

    /**
     * @param Version $version
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Version $version, PreUpdateEventArgs $event)
    {
        if ($version->isActive()) {
            $this->expireVersions($event->getObjectManager(), $version->getStorage());
        }
    }

    /**
     * @param Version $version
     * @param LifecycleEventArgs $event
     */
    public function preRemove(Version $version, LifecycleEventArgs $event)
    {
        if ($version->isActive()) {
            // Detach entity to prevent it removal
            $event->getObjectManager()->detach($version);
        }
    }

    /**
     * @param ObjectManager $manager
     * @param Storage $storage
     */
    private function expireVersions(ObjectManager $manager, Storage $storage)
    {
        $manager->getRepository(Version::class)->expireVersions($storage);
    }
}