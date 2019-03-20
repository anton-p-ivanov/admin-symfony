<?php

namespace App\Entity\Account;

use App\Entity\WorkflowTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="accounts")
 * @ORM\Entity(repositoryClass="App\Repository\Account\AccountRepository")
 */
class Account
{
    use WorkflowTrait;

    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", length=65536, nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default"=1})
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $web;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default"=100})
     */
    private $sort;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\User\Account", mappedBy="account")
     */
    private $users;

    /**
     * Account constructor.
     */
    public function __construct()
    {
        $defaults = [
            'isActive' => true,
            'sort' => 100,
        ];

        foreach ($defaults as $property => $value) {
            $this->$property = $value;
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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getWeb(): string
    {
        return $this->web;
    }

    /**
     * @param string $web
     */
    public function setWeb(string $web): void
    {
        $this->web = $web;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     */
    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers(): ArrayCollection
    {
        return $this->users;
    }

    /**
     * @param ArrayCollection $users
     */
    public function setUsers(ArrayCollection $users): void
    {
        $this->users = $users;
    }
}
