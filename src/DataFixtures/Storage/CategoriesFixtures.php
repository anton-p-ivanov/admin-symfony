<?php

namespace App\DataFixtures\Storage;

use App\Entity\Storage\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class CategoriesFixtures
 *
 * @package App\DataFixtures\Storage
 */
class CategoriesFixtures extends Fixture
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
        for ($index = 0; $index < 10; $index++) {
            $category = new Category();
            $attributes = [
                'title' => $this->faker->text(50),
                'description' => $this->faker->text(150),
                'isActive' => true,
                'sort' => 100 * ($index + 1),
            ];

            foreach ($attributes as $attribute => $value) {
                $category->{'set' . ucfirst($attribute)}($value);
            }

            $manager->persist($category);
        }

        $manager->flush();
    }
}