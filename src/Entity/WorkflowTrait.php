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
     * Cloning ...
     */
    public function __clone()
    {
        $this->workflow = null;
    }

    /**
     * @return Workflow|null
     */
    public function getWorkflow(): ?Workflow
    {
        return $this->workflow;
    }

    /**
     * @param Workflow|null $workflow
     */
    public function setWorkflow(?Workflow $workflow): void
    {
        $this->workflow = $workflow;
    }
}