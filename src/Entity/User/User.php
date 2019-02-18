<?php

namespace App\Entity\User;

use App\Entity\WorkflowTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\User\UserRepository")
 */
class User implements UserInterface, EquatableInterface, \Serializable
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    private $fname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    private $lname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $sname;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default"=1})
     */
    private $isActive;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    private $isConfirmed;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone_mobile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $skype;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthdate;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $comments;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\User\Password", mappedBy="user", cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    private $passwords;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\User\Checkword", mappedBy="user", cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    private $checkwords;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Role")
     * @ORM\JoinTable(name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="user_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_uuid", referencedColumnName="uuid")}
     * )
     */
    private $roles;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Site")
     * @ORM\JoinTable(name="users_sites",
     *     joinColumns={@ORM\JoinColumn(name="user_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="site_uuid", referencedColumnName="uuid")}
     * )
     */
    private $sites;

    /**
     * @var string|null
     */
    private $password;

    /**
     * @var string|null
     */
    private $salt;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $attributes = [
            'passwords' => new ArrayCollection(),
            'checkwords' => new ArrayCollection(),
            'isActive' => true,
            'isConfirmed' => false
        ];

        foreach ($attributes as $name => $value) {
            $this->$name = $value;
        }
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
    public function getFname(): string
    {
        return $this->fname;
    }

    /**
     * @param string $fname
     */
    public function setFname(string $fname): void
    {
        $this->fname = $fname;
    }

    /**
     * @return string
     */
    public function getLname(): string
    {
        return $this->lname;
    }

    /**
     * @param string $lname
     */
    public function setLname(string $lname): void
    {
        $this->lname = $lname;
    }

    /**
     * @return ArrayCollection
     */
    public function getPasswords(): Collection
    {
        return $this->passwords;
    }

    /**
     * @param Password $password
     */
    public function setPassword(Password $password): void
    {
        $this->getPasswords()->add($password);
    }

    /**
     * @return ArrayCollection
     */
    public function getCheckwords(): Collection
    {
        return $this->checkwords;
    }

    /**
     * @param Checkword $checkword
     */
    public function setCheckword(Checkword $checkword)
    {
        $this->getCheckwords()->add($checkword);
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles->map(function (\App\Entity\Role $role) { return $role->getCode(); })->toArray();
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = new ArrayCollection($roles);
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    /**
     * @param bool $isConfirmed
     */
    public function setIsConfirmed(bool $isConfirmed): void
    {
        $this->isConfirmed = $isConfirmed;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        $password = $this->getPasswords()->first();
        if ($password) {
            $this->password = $password->getPassword();
        }

        return $this->password;
    }

    /**
     * @return null|string
     */
    public function getSalt(): ?string
    {
        $password = $this->getPasswords()->first();
        if ($password) {
            $this->salt = $password->getSalt();
        }

        return $this->salt;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->email;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize([
            $this->uuid,
            $this->fname,
            $this->lname,
            $this->email,
            $this->roles,
        ]);
    }

    /**
     * @param $serialized
     *
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->uuid,
            $this->fname,
            $this->lname,
            $this->email,
            $this->roles
            ) = unserialize($serialized);
    }

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->email !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        /* @todo Implementation needed */
    }
}
