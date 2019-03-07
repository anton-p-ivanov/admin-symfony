<?php

namespace App\DataFixtures\Storage;

use App\DataFixtures\WorkflowStatusFixtures;
use App\Entity\Storage\Storage;
use App\Entity\Storage\Tree;
use App\Entity\Workflow;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class StorageFixtures
 *
 * @package App\DataFixtures\Storage
 */
class StorageFixtures extends Fixture implements DependentFixtureInterface
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
        $root = $this->makeRootNode();

        $manager->persist($root);
        $manager->flush();

        for ($index = 0; $index < 30; $index++) {
            $storage = new Storage();
            $attributes = [
                'title' => $this->faker->text(50),
                'description' => $this->faker->text(150),
                'content' => $this->faker->text(500),
                'workflow' => new Workflow(),
                'type' => $index > 4 ? Storage::STORAGE_TYPE_FILE : Storage::STORAGE_TYPE_DIR,
            ];

            foreach ($attributes as $attribute => $value) {
                $storage->{'set' . ucfirst($attribute)}($value);
            }

            $node = new Tree();
            $node->setParent($root);
            $node->setStorage($storage);

            $manager->persist($node);
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
            CategoriesFixtures::class
        ];
    }

    /**
     * @return Tree
     */
    protected function makeRootNode(): Tree
    {
        $root = new Tree();
        $attributes = [
            'leftMargin' => 1,
            'rightMargin' => 2,
            'level' => 0
        ];

        foreach ($attributes as $attribute => $value) {
            $root->{'set' . ucfirst($attribute)}($value);
        }

        return $root;
    }
}