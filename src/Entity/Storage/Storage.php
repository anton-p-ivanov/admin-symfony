<?php

namespace App\Entity\Storage;

use App\Entity\Workflow;
use App\Entity\WorkflowTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="storage")
 * @ORM\Entity()
 */
class Storage
{
    use WorkflowTrait;

    const STORAGE_TYPE_FILE = 'F';
    const STORAGE_TYPE_DIR = 'D';

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
     * @ORM\Column(type="string", length=1, options={"fixed"=true})
     */
    private $type;

    /**
     * @var string|null
     * 
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string|null
     * 
     * @ORM\Column(type="text", nullable=true, length=65535)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true, length=65535)
     */
    private $content;

    /**
     * @var Tree|null
     * 
     * @ORM\OneToOne(targetEntity="App\Entity\Storage\Tree", mappedBy="storage", cascade={"persist"})
     */
    private $node;

    /**
     * @var Tree
     */
    private $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Storage\Category")
     * @ORM\JoinTable(name="storage_categories_pivot",
     *     joinColumns={@ORM\JoinColumn(name="storage_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="category_uuid", referencedColumnName="uuid")}
     * )
     */
    private $categories;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Role")
     * @ORM\JoinTable(name="storage_roles",
     *     joinColumns={@ORM\JoinColumn(name="storage_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_uuid", referencedColumnName="uuid")}
     * )
     */
    private $roles;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Storage\Version", mappedBy="storage", cascade={"persist"})
     * @ORM\JoinColumn(name="storage_uuid", referencedColumnName="uuid")
     */
    private $versions;

    /**
     * Storage constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $defaults = [
            'type' => self::STORAGE_TYPE_DIR,
            'versions' => new ArrayCollection(),
            'categories' => new ArrayCollection(),
            'roles' => new ArrayCollection(),
            'workflow' => new Workflow()
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
     * @return null|string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
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
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return null|string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param null|string $content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isDirectory(): bool
    {
        return $this->type === self::STORAGE_TYPE_DIR;
    }

    /**
     * @return Tree|null
     */
    public function getNode(): ?Tree
    {
        return $this->node;
    }

    /**
     * @param Tree $node
     */
    public function setNode(Tree $node): void
    {
        $node->setStorage($this);

        $this->node = $node;
    }

    /**
     * @return ArrayCollection
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @param Collection $versions
     */
    public function setVersions(Collection $versions)
    {
        $this->versions = $versions;
    }

    /**
     * @param File $file
     */
    public function addVersion(File $file)
    {
        $version = new Version();
        $version->setFile($file);
        $version->setStorage($this);

        $this->getVersions()->add($version);
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        $versions = $this->versions->filter(function (Version $version) {
            return $version->isActive();
        });

        if (!$versions->isEmpty()) {
            return $versions->first()->getFile();
        }

        return null;
    }

    /**
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Collection $categories
     */
    public function setCategories(Collection $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @return Tree
     */
    public function getParent(): Tree
    {
        return $this->node->getParent();
    }

    /**
     * @param Tree $node
     */
    public function setParent(Tree $node): void
    {
        $this->node->setParent($node);
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @param Collection $roles
     */
    public function setRoles(Collection $roles): void
    {
        $this->roles = $roles;
    }
}
