<?php

namespace App\Listener\User;

use App\Entity\User\Checkword;
use App\Entity\User\User;
use App\Security\PasswordEncoder;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * Class CheckwordListener
 *
 * @package App\Listener\User
 */
class CheckwordListener
{
    /**
     * @var PasswordEncoder
     */
    private $encoder;

    /**
     * CheckwordListener constructor.
     *
     * @param PasswordEncoder $encoder
     */
    public function __construct(PasswordEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param Checkword $checkword
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Checkword $checkword, LifecycleEventArgs $event)
    {
        $checkword->setCheckword($this->encoder->encodePassword($checkword->getCheckword(), null));

        $event
            ->getObjectManager()
            ->getRepository(User::class)
            ->expireEntities($checkword->getUser(), Checkword::class);
    }
}