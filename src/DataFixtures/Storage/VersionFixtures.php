<?php

namespace App\DataFixtures\Storage;

use App\Entity\Storage\File;
use App\Entity\Storage\Storage;
use App\Entity\Storage\Version;
use App\Entity\Workflow;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class VersionFixtures
 *
 * @package App\DataFixtures\Storage
 */
class VersionFixtures extends Fixture implements DependentFixtureInterface
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
        $entities = $manager->getRepository(Storage::class)->findBy(['type' => Storage::STORAGE_TYPE_FILE]);

        foreach ($entities as $storage) {
            $max = random_int(1, 3);
            for ($i = 0; $i < $max; $i++) {
                $file = new File();
                $attributes = [
                    'name' => $this->prepareFileName(),
                    'size' => random_int(pow(1024, 1), pow(1024, 3)),
                    'type' => $this->faker->mimeType,
                    'hash' => md5(random_bytes(10)),
                    'workflow' => new Workflow()
                ];

                foreach ($attributes as $attribute => $value) {
                    $file->{'set' . ucfirst($attribute)}($value);
                }

                $version = new Version();
                $version->setStorage($storage);
                $version->setFile($file);
                $version->setIsActive($i === $max - 1);

                $storage->getVersions()->add($version);
            }

            $manager->persist($storage);
        }

        $manager->flush();
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            StorageFixtures::class
        ];
    }

    /**
     * @return string
     */
    protected function prepareFileName(): string
    {
        return preg_replace('/\s/', '_', strtolower($this->faker->text(25))) . $this->faker->fileExtension;
    }
}