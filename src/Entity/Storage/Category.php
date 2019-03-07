<?php

namespace App\Entity\Storage;

use App\Entity\WorkflowTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="storage_categories")
 * @ORM\Entity(repositoryClass="App\Repository\Storage\CategoryRepository")
 */
class Category
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
     * @ORM\Column(type="text", nullable=true, length=65536)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default"=1})
     */
    private $isActive;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default"=100})
     */
    private $sort;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $attributes = [
            'isActive' => true,
            'sort' => 100
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
}
