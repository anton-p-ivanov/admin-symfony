<?php

namespace App\DataFixtures;

use App\Entity\Account\Account;
use App\Entity\Workflow;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class AccountFixtures
 *
 * @package App\DataFixtures
 */
class AccountFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * TrainingFixtures constructor.
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
        for ($index = 0; $index < 50; $index++) {
            $account = new Account();
            $properties = [
                'title' => $this->faker->company,
                'description' => $this->faker->realText(),
                'email' => $this->faker->email,
                'web' => $this->faker->url,
                'phone' => $this->faker->phoneNumber,
                'workflow' => new Workflow(),
            ];

            foreach ($properties as $property => $value) {
                $account->{'set' . ucfirst($property)}($value);
            }

            $manager->persist($account);
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
            UserFixtures::class,
        ];
    }
}