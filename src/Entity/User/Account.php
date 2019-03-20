<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="users_accounts")
 * @ORM\Entity()
 */
class Account
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $uuid;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $position;

    /**
     * @var \App\Entity\User\User
     *
     * @ORM\OneToOne(targetEntity="App\Entity\User\User", inversedBy="account")
     * @ORM\JoinColumn(name="user_uuid", referencedColumnName="uuid")
     */
    private $user;

    /**
     * @var \App\Entity\Account\Account
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Account\Account", inversedBy="users")
     * @ORM\JoinColumn(name="account_uuid", referencedColumnName="uuid")
     */
    private $account;

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string|null
     */
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * @param string|null $position
     */
    public function setPosition(?string $position): void
    {
        $this->position = $position;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return \App\Entity\Account\Account|null
     */
    public function getAccount(): ?\App\Entity\Account\Account
    {
        return $this->account;
    }

    /**
     * @param \App\Entity\Account\Account $account
     */
    public function setAccount(?\App\Entity\Account\Account $account): void
    {
        $this->account = $account;
    }
}
