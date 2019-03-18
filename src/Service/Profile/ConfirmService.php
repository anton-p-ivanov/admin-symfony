<?php

namespace App\Service\Profile;

use App\Entity\Site;
use App\Entity\User as User;
use App\Security\PasswordEncoder;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ConfirmService
 *
 * @package App\Service\Profile
 */
class ConfirmService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PasswordEncoder
     */
    private $encoder;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var User\User
     */
    private $user;

    /**
     * @var string
     */
    private $nonSecuredCheckword;

    /**
     * @var MailService
     */
    private $mailer;

    /**
     * ResetService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param PasswordEncoder $encoder
     * @param TranslatorInterface $translator
     * @param MailService $mailer
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        PasswordEncoder $encoder,
        TranslatorInterface $translator,
        MailService $mailer)
    {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->translator = $translator;
        $this->mailer = $mailer;
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    public function confirm($form): bool
    {
        $user = $this->findUser($form->get('username')->getNormData());

        if (!$user) {
            $form->get('username')->addError(
                new FormError($this->translator->trans('form.confirm.username_not_found'))
            );

            return false;
        }

        $this->user = $user;

        $checkword = $this->validateCheckword($user, $form);
        $this->validatePassword($user, $form);

        if ($form->isValid()) {
            $user->setIsConfirmed(true);
            $checkword->setIsExpired(true);

            $this->entityManager->persist($user);
            $this->entityManager->persist($checkword);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    public function resend(FormInterface $form): bool
    {
        $user = $this->findUser($form->get('username')->getNormData());

        if (!$user) {
            $form->get('username')->addError(
                new FormError($this->translator->trans('form.confirm.username_not_found'))
            );

            return false;
        }

        $checkword = new User\Checkword($user);
        $this->nonSecuredCheckword = $checkword->getCheckword();

        $this->entityManager->persist($checkword);
        $this->entityManager->flush();

        $this->notify($user);

        return true;
    }

    /**
     * @param User\User $user
     */
    protected function notify(User\User $user)
    {
        $site = $this->entityManager
            ->getRepository(Site::class)
            ->findOneBy(['code' => 'ADMIN']);

        if ($site) {
            $this->mailer->template('USER_REGISTER')->send([
                'USER_EMAIL' => $user->getEmail(),
                'USER_CHECKWORD' => $this->nonSecuredCheckword,
                'CLIENT_ID' => $site->getUuid()
            ]);
        }
    }

    /**
     * @param string|null $username
     *
     * @return User\User|null
     */
    protected function findUser(?string $username): ?User\User
    {
        if (!$username) {
            return null;
        }

        return $this->entityManager->getRepository(User\User::class)->findOneBy([
            'email' => $username
        ]);
    }

    /**
     * @return User\User
     */
    public function getUser(): User\User
    {
        return $this->user;
    }

    /**
     * @param User\User $user
     * @param FormInterface $form
     *
     * @return User\Checkword|null
     */
    protected function validateCheckword(User\User $user, FormInterface $form): ?User\Checkword
    {
        $isValid = false;

        /* @var User\Checkword $checkword */
        $checkword = $user->getCheckwords()->first();
        if ($checkword) {
            $isValid = $this->encoder->isPasswordValid(
                $checkword->getCheckword(),
                $form->get('checkword')->getNormData(),
                null
            );
        }

        if (!$checkword || $checkword->isExpired() || !$isValid) {
            $form->get('checkword')->addError(new FormError(
                $this->translator->trans('form.confirm.invalid_checkword'))
            );
        }

        return $checkword;
    }

    /**
     * @param User\User $user
     * @param FormInterface $form
     */
    protected function validatePassword(User\User $user, FormInterface $form): void
    {
        $isValid = false;

        /* @var User\Password $password */
        $password = $user->getPasswords()->first();
        if ($password) {
            $isValid = $this->encoder->isPasswordValid(
                $password->getPassword(),
                $form->get('password')->getNormData(),
                $password->getSalt()
            );
        }

        if (!$password || $password->isExpired() || !$isValid) {
            $form->get('password')->addError(new FormError(
                $this->translator->trans('form.confirm.invalid_password'))
            );
        }
    }
}