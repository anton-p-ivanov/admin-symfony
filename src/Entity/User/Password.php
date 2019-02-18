<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="users_passwords")
 * @ORM\Entity()
 * @ORM\EntityListeners({"App\Listener\User\PasswordListener"})
 */
class Password
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
     * @var string
     *
     * @ORM\Column(type="string", length=60, options={"fixed"=true})
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, options={"fixed"=true})
     */
    private $salt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiredAt;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    private $isExpired;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User", inversedBy="passwords")
     * @ORM\JoinColumn(name="user_uuid", referencedColumnName="uuid", nullable=false)
     */
    private $user;

    /**
     * Password constructor.
     * @param string $password
     * @param User $user
     * @throws \Exception
     */
    public function __construct(string $password, User $user)
    {
        $attributes = [
            'password' => $password,
            'salt' => bin2hex(random_bytes(10)),
            'user' => $user,
            'isExpired' => false,
            'createdAt' => new \DateTime(),
            'expiredAt' => (new \DateTime())->modify('+1 year')
        ];

        foreach ($attributes as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getExpiredAt(): \DateTime
    {
        return $this->expiredAt;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->isExpired || ($this->expiredAt < new \DateTime());
    }
}
