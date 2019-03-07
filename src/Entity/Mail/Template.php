<?php

namespace App\Entity\Mail;

use App\Entity\Site;
use App\Entity\WorkflowTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="mail_templates")
 * @ORM\Entity()
 */
class Template
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
     * @Gedmo\Slug(fields={"subject"}, updatable=false, separator="_", style="upper")
     */
    private $code;

    /**
     * @var string|null
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sender;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     */
    private $recipient;

    /**
     * @var string|null
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $replyTo;

    /**
     * @var string|null
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $copyTo;

    /**
     * @var bool
     * 
     * @ORM\Column(type="boolean", options={"default"=1})
     */
    private $isActive;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     */
    private $subject;

    /**
     * @var string|null
     * 
     * @ORM\Column(type="text", nullable=true, length=65536)
     */
    private $textBody;

    /**
     * @var string|null
     * 
     * @ORM\Column(type="text", nullable=true, length=65536)
     */
    private $htmlBody;

    /**
     * @var Type
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Mail\Type", inversedBy="templates", cascade={"persist"})
     * @ORM\JoinColumn(name="type_uuid", referencedColumnName="uuid", nullable=false)
     */
    private $type;

    /**
     * @var ArrayCollection
     * 
     * @ORM\ManyToMany(targetEntity="App\Entity\Site")
     * @ORM\JoinTable(name="mail_templates_sites",
     *     joinColumns={@ORM\JoinColumn(name="template_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="site_uuid", referencedColumnName="uuid")})
     */
    private $sites;

    /**
     * Template constructor.
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->sites = new ArrayCollection();
    }

    /**
     * Template clone.
     */
    public function __clone()
    {
        $this->uuid = null;
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
     * @param string|null $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string|null
     */
    public function getSender(): ?string
    {
        return $this->sender;
    }

    /**
     * @param string|null $sender
     */
    public function setSender(?string $sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * @param string $recipient
     */
    public function setRecipient(string $recipient): void
    {
        $this->recipient = $recipient;
    }

    /**
     * @return null|string
     */
    public function getReplyTo(): ?string
    {
        return $this->replyTo;
    }

    /**
     * @param null|string $replyTo
     */
    public function setReplyTo(?string $replyTo): void
    {
        $this->replyTo = $replyTo;
    }

    /**
     * @return null|string
     */
    public function getCopyTo(): ?string
    {
        return $this->copyTo;
    }

    /**
     * @param null|string $copyTo
     */
    public function setCopyTo(?string $copyTo): void
    {
        $this->copyTo = $copyTo;
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
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return null|string
     */
    public function getTextBody(): ?string
    {
        return $this->textBody;
    }

    /**
     * @param null|string $textBody
     */
    public function setTextBody(?string $textBody): void
    {
        $this->textBody = $textBody;
    }

    /**
     * @return null|string
     */
    public function getHtmlBody(): ?string
    {
        return $this->htmlBody;
    }

    /**
     * @param null|string $htmlBody
     */
    public function setHtmlBody(?string $htmlBody): void
    {
        $this->htmlBody = $htmlBody;
    }

    /**
     * @return Type|null
     */
    public function getType(): ?Type
    {
        return $this->type;
    }

    /**
     * @param Type $type
     */
    public function setType(Type $type): void
    {
        $this->type = $type;
    }

    /**
     * @return ArrayCollection
     */
    public function getSites(): Collection
    {
        return $this->sites;
    }

    /**
     * @param Site[] $sites
     */
    public function setSites(array $sites): void
    {
        $this->sites = $sites;
    }
}
