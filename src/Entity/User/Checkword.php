<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="users_checkwords")
 * @ORM\Entity()
 * @ORM\EntityListeners({"App\Listener\User\CheckwordListener"})
 */
class Checkword
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
     * @ORM\Column(type="string", length=60, options={"fixed"=true}, unique=true)
     */
    private $checkword;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User", inversedBy="checkwords")
     * @ORM\JoinColumn(name="user_uuid", referencedColumnName="uuid", nullable=false)
     */
    private $user;

    /**
     * Checkword constructor.
     * @param User $user
     * @throws \Exception
     */
    public function __construct(User $user)
    {
        $attributes = [
            'user' => $user,
            'checkword' => bin2hex(random_bytes(5)),
            'createdAt' => new \DateTime(),
            'expiredAt' => (new \DateTime())->modify('+1 day'),
            'isExpired' => false
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
    public function getCheckword(): string
    {
        return $this->checkword;
    }

    /**
     * @param string $checkword
     */
    public function setCheckword(string $checkword): void
    {
        $this->checkword = $checkword;
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

    /**
     * @param bool $isExpired
     */
    public function setIsExpired(bool $isExpired): void
    {
        $this->isExpired = $isExpired;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
