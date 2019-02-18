<?php

namespace App\Listener\User;

use App\Entity\User\User;
use App\Entity\User\Checkword;
use App\Entity\User\Password;
use App\Security\PasswordEncoder;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * Class PasswordListener
 *
 * @package App\Listener\User
 */
class PasswordListener
{
    /**
     * @var PasswordEncoder
     */
    private $encoder;

    /**
     * UserCheckwordListener constructor.
     *
     * @param PasswordEncoder $encoder
     */
    public function __construct(PasswordEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param Password $password
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Password $password, LifecycleEventArgs $event)
    {
        // Encode password
        $password->setPassword($this->encoder->encodePassword(
            $password->getPassword(),
            $password->getSalt()
        ));

        $repository = $event->getObjectManager()->getRepository(User::class);

        // Expire all previous user passwords
        $repository->expireEntities($password->getUser(), Password::class);

        // Expire all previous user checkwords
        $repository->expireEntities($password->getUser(), Checkword::class);
    }
}