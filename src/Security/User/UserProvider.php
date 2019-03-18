<?php

namespace App\Security\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception as Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 *
 * @package App\Security\User
 */
class UserProvider implements UserProviderInterface
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
     * @param string $username
     *
     * @return User|UserInterface
     */
    public function loadUserByUsername($username)
    {
        return $this->fetchUser($username);
    }

    /**
     * @param UserInterface $user
     *
     * @return User|UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new Exception\UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        $username = $user->getUsername();

        return $this->fetchUser($username);
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return User::class === $class;
    }

    /**
     * @param string $username
     *
     * @return User|null
     */
    private function fetchUser(string $username): ?User
    {
        /* @var \App\Entity\User\User $userData */
        $userData = $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(\App\Entity\User\User::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();

        if ($userData) {
            $password = $salt = null;

            if ($userData->getPasswords()->count()) {
                $userPassword = $userData->getPasswords()->first();

                $password = $userPassword->getPassword();
                $salt = $userPassword->getSalt();
            }

            $roles = $userData->getRoles();

            return new User([
                'fullName' => $userData->getFullName(),
                'username' => $username,
                'password' => $password,
                'salt' => $salt,
                'roles' => $roles,
                'isActive' => $userData->isActive(),
                'isConfirmed' => $userData->isConfirmed(),
            ]);
        }

        return null;
    }
}