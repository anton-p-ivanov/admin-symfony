<?php

namespace App\DataFixtures;

use App\Entity\User\User;
use App\Entity\Workflow;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures
 *
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var Factory
     */
    private $faker;

    /**
     * UserPasswordFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;

        $this->faker = Factory::create();
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $roles = $manager->getRepository('App:Role')->findBy(['code' => 'ROLE_USER']);
        $sites = $manager->getRepository('App:Site')->findBy([], [], 2);

        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $attributes = [
                'email' => $this->faker->email,
                'fname' => $this->faker->firstName,
                'lname' => $this->faker->lastName,
                'phone' => $this->faker->phoneNumber,
                'phoneMobile' => $this->faker->phoneNumber,
                'birthdate' => $this->faker->dateTime(),
                'isConfirmed' => true,
                'roles' => new ArrayCollection($roles),
                'sites' => new ArrayCollection($sites),
                'workflow' => new Workflow(),
            ];

            foreach ($attributes as $name => $value) {
                $user->{'set'.ucfirst($name)}($value);
            }

            $manager->persist($user);
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
            MailTemplateFixtures::class,
            RoleFixtures::class
        ];
    }
}