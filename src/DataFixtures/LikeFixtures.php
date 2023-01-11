<?php

namespace App\DataFixtures;

use App\Entity\Like;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LikeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $rand = rand(0, 50);
            for ($j = 0; $j < $rand; $j++) {
                $like = new Like();
                $like->setVideo($this->getReference('video_' . $i));
                $like->setUser($this->getReference('user_' . $j));
                $like->setCreatedAt($faker->dateTimeBetween('-6 month'));
                $manager->persist($like);
            }
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            VideoFixtures::class,
            UserFixtures::class
        ];
    }
}
