<?php

namespace App\Entity\Storage;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="storage_images")
 * @ORM\Entity()
 */
class Image
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
     * @ORM\OneToOne(targetEntity="App\Entity\Storage\File")
     * @ORM\JoinColumn(name="file_uuid", referencedColumnName="uuid")
     */
    private $file;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $height;

    /**
     * @return null|string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }
}
