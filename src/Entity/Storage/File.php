<?php

namespace App\Entity\Storage;

use App\Entity\Workflow;
use App\Entity\WorkflowTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="storage_files")
 * @ORM\EntityListeners({"App\Listener\Storage\FileListener"})
 * @ORM\Entity()
 */
class File
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
    private $name;

    /**
     * @var int
     * 
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=32, options={"fixed"=true})
     */
    private $hash;

    /**
     * @var array
     */
    private $options = [];

    /**
     * File constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $defaults = [
            'workflow' => new Workflow()
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
     * @return null|string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
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
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }
}
