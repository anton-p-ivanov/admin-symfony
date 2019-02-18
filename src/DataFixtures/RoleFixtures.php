<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class RoleFixtures
 *
 * @package App\DataFixtures
 */
class RoleFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $roles = json_decode(file_get_contents(__DIR__ . '/Data/Roles.json'));

        foreach ($roles as $attributes) {
            $role = new Role();
            foreach ($attributes as $name => $value) {
                $role->{'set' . ucfirst($name)}($value);
            }

            $manager->persist($role);
        }

        $manager->flush();
    }
}