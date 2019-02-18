<?php

namespace App\DataFixtures;

use App\Entity\Mail\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class MailTypeFixtures
 *
 * @package App\DataFixtures
 */
class MailTypeFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $types = json_decode(file_get_contents(__DIR__ . '/Data/MailType.json'), true);

        foreach ($types as $attributes) {
            $type = new Type();
            foreach ($attributes as $name => $value) {
                $type->{'set' . ucfirst($name)}($value);
            }

            $manager->persist($type);
            $this->addReference('MAILTYPE_' . strtoupper($attributes['code']), $type);
        }

        $manager->flush();
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            WorkflowStatusFixtures::class,
        ];
    }
}