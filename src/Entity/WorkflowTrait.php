<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait WorkflowTrait
 *
 * @package App\Entity
 */
trait WorkflowTrait
{
    /**
     * @var Workflow|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Workflow", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="workflow_uuid", referencedColumnName="uuid", nullable=true)
     */
    private $workflow;

    /**
     * @var WorkflowStatus
     */
    private $status;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * Clone method
     */
    public function __clone()
    {
        $this->workflow = null;
    }

    /**
     * @return Workflow
     */
    public function getWorkflow(): Workflow
    {
        return $this->workflow ?? new Workflow();
    }

    /**
     * @param Workflow $workflow
     */
    public function setWorkflow(Workflow $workflow): void
    {
        $this->workflow = $workflow;
    }

    /**
     * @return WorkflowStatus|null
     */
    public function getStatus(): ?WorkflowStatus
    {
        return $this->getWorkflow()->getStatus();
    }

    /**
     * @param WorkflowStatus $status
     */
    public function setStatus(WorkflowStatus $status): void
    {
        $this->getWorkflow()->setStatus($status);
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->getWorkflow()->getUpdatedAt();
    }

    /**
     * @param string $updatedAt
     */
    public function setUpdatedAt(string $updatedAt): void
    {
        $this->getWorkflow()->setUpdatedAt((new \DateTime())->setTimestamp((int) $updatedAt));
    }
}