<?php

namespace App\Service\Profile;

use App\Entity\User as User;
use App\Security\PasswordEncoder;
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
     * ResetService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param PasswordEncoder $encoder
     * @param TranslatorInterface $translator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        PasswordEncoder $encoder,
        TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->translator = $translator;
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    public function confirm($form): bool
    {
        /* @var User\User $user */
        $user = $this->entityManager->getRepository(User\User::class)->findOneBy([
            'email' => $form->get('username')->getNormData()
        ]);

        if (!$user) {
            $form->get('username')->addError(
                new FormError($this->translator->trans('form.confirm.username_not_found'))
            );
            return false;
        }

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