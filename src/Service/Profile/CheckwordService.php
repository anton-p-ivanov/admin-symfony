<?php

namespace App\Service\Profile;

use App\Entity\Site;
use App\Entity\User as User;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CheckwordService
 *
 * @package App\Service\Profile
 */
class CheckwordService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

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
     * @param TranslatorInterface $translator
     * @param MailService $mailer
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        MailService $mailer)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->mailer = $mailer;
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
                new FormError($this->translator->trans('form.checkword.username_not_found'))
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
     * @param string $username
     *
     * @return User\User|null
     */
    protected function findUser(string $username): ?User\User
    {
        return $this->entityManager->getRepository(User\User::class)->findOneBy([
            'email' => $username
        ]);
    }
}