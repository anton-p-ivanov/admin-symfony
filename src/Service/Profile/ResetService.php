<?php

namespace App\Service\Profile;

use App\Entity\Site;
use App\Entity\User\Checkword;
use App\Entity\User\User;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ResetService
 *
 * @package App\Service\Profile
 */
class ResetService
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
     * @param string $username
     *
     * @return bool
     */
    public function reset(string $username): bool
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $username]);

        if ($user) {
            $checkword = new Checkword($user);
            $this->nonSecuredCheckword = $checkword->getCheckword();

            $this->entityManager->persist($checkword);
            $this->entityManager->flush();

            $this->notify($user);

            return true;
        }

        return false;
    }

    /**
     * @param User $user
     */
    protected function notify(User $user)
    {
        $site = $this->entityManager
            ->getRepository(Site::class)
            ->findOneBy(['code' => 'ADMIN']);

        $this->mailer->template('USER_RESET')->send([
            'USER_EMAIL' => $user->getEmail(),
            'USER_CHECKWORD' => $this->nonSecuredCheckword,
            'CLIENT_ID' => $site->getUuid()
        ]);
    }
}