<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class TagFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 30; $i++) {
            $tag = new Tag();
            $tag->setName($faker->word);
            $tag->setCreatedAt($faker->dateTimeBetween('-6 month'));
            $rand = rand(1, 3);
            for ($j = 0; $j <= $rand; $j++) {
                $tag->addVideo($this->getReference('video_' . $faker->numberBetween(0, 49)));
            }
            $manager->persist($tag);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            VideoFixtures::class,
        ];
    }
}
