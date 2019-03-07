<?php

namespace App\Entity\Mail;

use App\Entity\WorkflowTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="mail_types")
 * @ORM\Entity()
 */
class Type
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
     * @Gedmo\Slug(fields={"title"}, updatable=false, separator="_", style="upper")
     */
    private $code;

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
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="App\Entity\Mail\Template", mappedBy="type", cascade={"persist", "remove"})
     */
    private $templates;

    /**
     * Type constructor.
     */
    public function __construct()
    {
        $this->templates = new ArrayCollection();
    }

    /**
     * Mail type clone.
     */
    public function __clone()
    {
        // Unset primary key
        $this->uuid = null;
    }

    /**
     * @return ArrayCollection
     */
    public function getTemplates(): ArrayCollection
    {
        return $this->templates;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return null|string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
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
}
