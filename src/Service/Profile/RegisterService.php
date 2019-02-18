<?php

namespace App\Service\Profile;

use App\Entity\Role;
use App\Entity\Site;
use App\Entity\User\Checkword;
use App\Entity\User\Password;
use App\Entity\User\User;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class RegisterService
 *
 * @package App\Service\Profile
 */
class RegisterService
{
    /**
     * @var MailService
     */
    private $mailer;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $nonSecuredCheckword;

    /**
     * ResetService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param MailService $mailer
     */
    public function __construct(EntityManagerInterface $entityManager, MailService $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    public function register(FormInterface $form): bool
    {
        // Looking for existing user
        $entity = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $form->get('username')->getNormData()]);

        if ($entity) {
            return false;
        }

        $user = $this->getUser($form->getNormData());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->notify($user);

        return true;
    }

    /**
     * @param array $data
     *
     * @return User
     */
    protected function getUser(array $data): User
    {
        $roles = $this->entityManager
            ->getRepository(Role::class)
            ->findBy(['code' => 'ROLE_USER']);

        $user = new User();

        $attributes = [
            'email' => $data['username'],
            'fname' => $data['fname'],
            'lname' => $data['lname'],
            'roles' => $roles,
            'password' => new Password($data['password'], $user),
            'checkword' => new Checkword($user)
        ];

        $this->nonSecuredCheckword = $attributes['checkword']->getCheckword();

        foreach ($attributes as $name => $value) {
            $user->{'set' . ucfirst($name)}($value);
        }

        return $user;
    }

    /**
     * @param User $user
     */
    protected function notify(User $user)
    {
        $site = $this->entityManager
            ->getRepository(Site::class)
            ->findOneBy(['code' => 'ADMIN']);

        $this->mailer->template('USER_REGISTER')->send([
            'USER_EMAIL' => $user->getEmail(),
            'USER_CHECKWORD' => $this->nonSecuredCheckword,
            'CLIENT_ID' => $site->getUuid()
        ]);
    }
}