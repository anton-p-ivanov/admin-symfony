<?php

namespace App\Entity\User;

use App\Entity\Workflow;
use App\Entity\WorkflowTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\User\UserRepository")
 *
 * @UniqueEntity(fields={"email"})
 */
class User
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
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone_mobile;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $skype;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthdate;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true, length=65536)
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
     * @var \App\Entity\User\Account|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\User\Account", mappedBy="user", cascade={"persist"})
     */
    private $account;

    /**
     * @var string
     */
    private $password;

    /**
     * User constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $defaults = [
            'passwords' => new ArrayCollection(),
            'checkwords' => new ArrayCollection(),
            'roles' => new ArrayCollection(),
            'sites' => new ArrayCollection(),
            'isActive' => true,
            'isConfirmed' => false,
        ];

        $attributes = array_merge($defaults, $attributes);

        foreach ($attributes as $attribute => $value) {
            $method = strpos($attribute, '+') === 0
                ? 'add' . ucfirst(substr($attribute, 1))
                : 'set' . ucfirst($attribute);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * User cloning.
     */
    public function __clone()
    {
        $attributes = [
            'passwords' => new ArrayCollection(),
            'checkwords' => new ArrayCollection(),
            'workflow' => new Workflow()
        ];

        foreach ($attributes as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
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
     * @return string|null
     */
    public function getFname(): ?string
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
     * @return string|null
     */
    public function getLname(): ?string
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
     * @return string|null
     */
    public function getSname(): ?string
    {
        return $this->sname;
    }

    /**
     * @param string|null $sname
     */
    public function setSname(?string $sname): void
    {
        $this->sname = $sname;
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
     * @return string|null
     */
    public function getPhoneMobile(): ?string
    {
        return $this->phone_mobile;
    }

    /**
     * @param string|null $phone_mobile
     */
    public function setPhoneMobile(?string $phone_mobile): void
    {
        $this->phone_mobile = $phone_mobile;
    }

    /**
     * @return string|null
     */
    public function getSkype(): ?string
    {
        return $this->skype;
    }

    /**
     * @param string|null $skype
     */
    public function setSkype(?string $skype): void
    {
        $this->skype = $skype;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthdate(): ?\DateTime
    {
        return $this->birthdate;
    }

    /**
     * @param \DateTime|null $birthdate
     */
    public function setBirthdate(?\DateTime $birthdate): void
    {
        $this->birthdate = $birthdate;
    }

    /**
     * @return string|null
     */
    public function getComments(): ?string
    {
        return $this->comments;
    }

    /**
     * @param string|null $comments
     */
    public function setComments(?string $comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return ArrayCollection
     */
    public function getSites(): ?Collection
    {
        return $this->sites;
    }

    /**
     * @param ArrayCollection $sites
     */
    public function setSites(ArrayCollection $sites): void
    {
        $this->sites = $sites;
    }

    /**
     * @return ArrayCollection
     */
    public function getPasswords(): Collection
    {
        return $this->passwords;
    }

    /**
     * @param ArrayCollection $passwords
     */
    public function setPasswords(ArrayCollection $passwords): void
    {
        $this->passwords = $passwords;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
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
     * @param ArrayCollection $checkwords
     */
    public function setCheckwords(ArrayCollection $checkwords): void
    {
        $this->checkwords = $checkwords;
    }

    /**
     * @param Checkword $checkword
     */
    public function setCheckword(Checkword $checkword)
    {
        $this->getCheckwords()->add($checkword);
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @param ArrayCollection $roles
     */
    public function setRoles(ArrayCollection $roles): void
    {
        $this->roles = $roles;
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
     * @return string
     */
    public function getFullName(): string
    {
        return trim($this->fname . ' ' . $this->lname);
    }

    /**
     * @return Account|null
     */
    public function getAccount(): ?Account
    {
        return $this->account;
    }

    /**
     * @param Account $account
     */
    public function setAccount(Account $account): void
    {
        if (!$account->getUser()) {
            $account->setUser($this);
        }

        $this->account = $account;
    }
}
