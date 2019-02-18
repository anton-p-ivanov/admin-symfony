<?php

namespace App\DataFixtures;

use App\Entity\Mail\Template;
use App\Entity\Mail\Type;
use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class MailTemplateFixtures
 *
 * @package App\DataFixtures
 */
class MailTemplateFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /* @var $type Type */
        $type = $this->getReference('MAILTYPE_SYSTEM');

        $sites = $manager->getRepository(Site::class)->findAll();
        $templates = json_decode(file_get_contents(__DIR__ . '/Data/MailTemplates.json'), true);

        foreach ($templates as $attributes) {
            $attributes = array_merge($attributes, [
                'type' => $type,
                'textBody' => file_get_contents(__DIR__ . "/Mail/" . $attributes['code']),
                'sites' => $sites
            ]);

            $template = new Template();
            foreach ($attributes as $name => $value) {
                $template->{'set' . ucfirst($name)}($value);
            }

            $manager->persist($template);
        }

        $manager->flush();
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            MailTypeFixtures::class
        ];
    }
}