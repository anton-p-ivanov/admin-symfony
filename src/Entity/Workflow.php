<?php

namespace App\Entity;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="workflow")
 * @ORM\Entity()
 */
class Workflow
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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    private $isDeleted;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="uuid", nullable=true)
     */
    private $created;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="uuid", nullable=true)
     */
    private $updated;

    /**
     * @var WorkflowStatus|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\WorkflowStatus")
     * @ORM\JoinColumn(name="status_uuid", referencedColumnName="uuid", nullable=true)
     */
    private $status;

    /**
     * Workflow constructor.
     */
    public function __construct()
    {
        $attributes = [
            'isDeleted' => false,
            'createdAt' => new \DateTime(),
            'updatedAt' => new \DateTime(),
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
     * @return User|null
     */
    public function getUpdated(): ?User
    {
        return $this->updated;
    }

    /**
     * @param User|null $user
     */
    public function setUpdated(?User $user): void
    {
        $this->updated = $user;
    }

    /**
     * @return User|null
     */
    public function getCreated(): ?User
    {
        return $this->created;
    }

    /**
     * @param User|null $user
     */
    public function setCreated(?User $user): void
    {
        $this->updated = $user;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return WorkflowStatus|null
     */
    public function getStatus(): ?WorkflowStatus
    {
        return $this->status;
    }

    /**
     * @param WorkflowStatus $status
     */
    public function setStatus(WorkflowStatus $status): void
    {
        $this->status = $status;
    }
}
