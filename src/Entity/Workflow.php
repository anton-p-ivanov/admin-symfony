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
}
