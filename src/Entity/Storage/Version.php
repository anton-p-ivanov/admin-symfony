<?php

namespace App\Entity\Storage;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="storage_versions")
 * @ORM\Entity(repositoryClass="App\Repository\Storage\VersionRepository")
 * @ORM\EntityListeners({"App\Listener\Storage\VersionListener"})
 */
class Version
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
     * @var File
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Storage\File", cascade={"persist"})
     * @ORM\JoinColumn(name="file_uuid", referencedColumnName="uuid")
     */
    private $file;

    /**
     * @var Storage
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Storage\Storage", inversedBy="versions")
     * @ORM\JoinColumn(name="storage_uuid", referencedColumnName="uuid")
     */
    private $storage;

    /**
     * @var bool
     * 
     * @ORM\Column(type="boolean", options={"default"=1})
     */
    private $isActive;

    /**
     * Version constructor.
     */
    public function __construct()
    {
        $defaults = [
            'isActive' => true,
        ];

        foreach ($defaults as $property => $value) {
            $this->$property = $value;
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
     * @return Storage
     */
    public function getStorage(): Storage
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
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file): void
    {
        $this->file = $file;
    }
}
