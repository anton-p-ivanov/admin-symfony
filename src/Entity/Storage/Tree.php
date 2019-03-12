<?php

namespace App\Entity\Storage;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="storage_tree")
 * @ORM\Entity(repositoryClass="App\Repository\Storage\TreeRepository")
 * @ORM\EntityListeners({"App\Listener\Storage\TreeListener"})
 *
 * @Gedmo\Tree(type="nested")
 */
class Tree
{
    /**
     * @var string
     * 
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @var Storage
     * 
     * @ORM\OneToOne(targetEntity="App\Entity\Storage\Storage", inversedBy="node")
     * @ORM\JoinColumn(name="storage_uuid", referencedColumnName="uuid")
     */
    private $storage;

    /**
     * @var int
     * 
     * @ORM\Column(type="integer")
     * @Gedmo\TreeLeft()
     */
    private $leftMargin;

    /**
     * @var int
     * 
     * @ORM\Column(type="integer")
     * @Gedmo\TreeRight()
     */
    private $rightMargin;

    /**
     * @var int
     * 
     * @ORM\Column(type="integer")
     * @Gedmo\TreeLevel()
     */
    private $level;

    /**
     * @var Tree
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Storage\Tree")
     * @ORM\JoinColumn(name="root_uuid", referencedColumnName="uuid")
     * @Gedmo\TreeRoot()
     */
    private $root;

    /**
     * @var Tree
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Storage\Tree", inversedBy="children")
     * @ORM\JoinColumn(name="parent_uuid", referencedColumnName="uuid")
     * @Gedmo\TreeParent()
     */
    private $parent;

    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="App\Entity\Storage\Tree", mappedBy="parent")
     * @ORM\OrderBy({"leftMargin" = "ASC"})
     */
    private $children;

    /**
     * Tree constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $defaults = [
            'children' => new ArrayCollection()
        ];

        $attributes = array_merge($defaults, $attributes);

        foreach ($attributes as $attribute => $value) {
            $method = 'set' . ucfirst($attribute);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
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
     * @return Tree
     */
    public function getRoot(): Tree
    {
        return $this->root;
    }

    /**
     * @return Tree|null
     */
    public function getParent(): ?Tree
    {
        return $this->parent;
    }

    /**
     * @param Tree|null $parent
     */
    public function setParent(?Tree $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return Storage|null
     */
    public function getStorage(): ?Storage
    {
        return $this->storage;
    }

    /**
     * @param Storage $storage
     */
    public function setStorage(Storage $storage): void
    {
        $this->storage = $storage;
    }

    /**
     * @return int
     */
    public function getLeftMargin(): int
    {
        return $this->leftMargin;
    }

    /**
     * @return int
     */
    public function getRightMargin(): int
    {
        return $this->rightMargin;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param int $leftMargin
     */
    public function setLeftMargin(int $leftMargin): void
    {
        $this->leftMargin = $leftMargin;
    }

    /**
     * @param int $rightMargin
     */
    public function setRightMargin(int $rightMargin): void
    {
        $this->rightMargin = $rightMargin;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }
}
