<?php

namespace App\Entity\Storage;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="storage_requests")
 * @ORM\Entity()
 */
class Request
{
    const STATUS_WAITING = 'W';
    const STATUS_GRANTED = 'G';
    const STATUS_DENIED  = 'D';

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Storage\Storage")
     * @ORM\JoinColumn(name="storage_uuid", referencedColumnName="uuid")
     */
    private $storage;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     * @ORM\JoinColumn(name="user_uuid", referencedColumnName="uuid")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1, options={"fixed"=true})
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiredAt;
}
