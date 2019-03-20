<?php

namespace App\DataFixtures;

use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class SiteFixtures
 *
 * @package App\DataFixtures
 */
class SiteFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var Factory
     */
    private $faker;

    /**
     * SiteFixtures constructor.
     */
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $sites = json_decode(file_get_contents(__DIR__ . '/Data/Sites.json'));

        foreach ($sites as $attributes) {
            $site = new Site();
            foreach ($attributes as $name => $value) {
                $site->{'set' . ucfirst($name)}($value);
                $site->setDescription($this->faker->text());
            }

            $manager->persist($site);
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