<?php

namespace App\DataFixtures;

use App\Entity\WorkflowStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class WorkflowStatusFixtures
 *
 * @package App\DataFixtures
 */
class WorkflowStatusFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $statuses = json_decode(file_get_contents(__DIR__ . '/Data/WorkflowStatus.json'));

        foreach ($statuses as $index => $status) {
            $workflowStatus = new WorkflowStatus();

            foreach ($status as $name => $value) {
                $workflowStatus->{'set' . ucfirst($name)}($value);
            }

            $manager->persist($workflowStatus);
        }

        $manager->flush();
    }
}