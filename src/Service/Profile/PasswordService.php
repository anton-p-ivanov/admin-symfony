<?php

namespace App\Service\Profile;

use App\Entity\User as User;
use App\Security\PasswordEncoder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PasswordService
 *
 * @package App\Service\Profile
 */
class PasswordService
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
    public function update($form): bool
    {
        /* @var User\User $user */
        $user = $this->entityManager->getRepository(User\User::class)->findOneBy([
            'email' => $form->get('username')->getNormData()
        ]);

        if (!$user) {
            $form->get('username')->addError(
                new FormError($this->translator->trans('form.password.username_not_found'))
            );
            return false;
        }

        if ($this->isCheckwordValid($user, $form)) {
            $password = new User\Password($form->get('password')->getNormData(), $user);

            $this->entityManager->persist($password);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * @param User\User $user
     * @param FormInterface $form
     *
     * @return bool
     */
    protected function isCheckwordValid(User\User $user, FormInterface $form): bool
    {
        /* @var User\Checkword $checkword */
        $checkword = $user->getCheckwords()->first();
        if (!$checkword) {
            return false;
        }

        $isValid = $this->encoder->isPasswordValid(
            $checkword->getCheckword(),
            $form->get('checkword')->getNormData(),
            null
        );

        if (!$checkword->isExpired() && $isValid) {
            return true;
        }

        $form->get('checkword')->addError(new FormError(
            $this->translator->trans('form.password.invalid_checkword'))
        );

        return false;
    }
}