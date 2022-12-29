<?php

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class VideoFixtures extends Fixture implements DependentFixtureInterface
{
    public const CATEGORIES = [
        'Football',
        'Basketball',
        'Tennis',
        'Volleyball',
        'Handball',
        'Hockey sur glace',
        'Rugby',
        'MMA',
        'Choco Week',
        'Boxe'
    ];

    public const USER = [
        'admin',
        'superAdmin'
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $video = new Video();
            $video->setTitle($faker->words(3, true));
            $video->setDescription($faker->paragraphs(1, true));
            $video->setIsPrivate($faker->boolean);
            $video->setNumberOfView($faker->numberBetween(8, 1052));
            $video->setUrl($faker->url);
            $video->setTeaser($faker->url);
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[$faker->numberBetween(0, 9)]));
            $video->setCreatedAt($faker->dateTimeBetween('-6 month'));

            $randomUser = rand(0, 1);
            $video->setUser($this->getReference(self::USER[$randomUser]));

            $nbLike = rand(0, 10);
            for ($j = 0; $j < $nbLike; $j++) {
                $video->addLikedByUser($this->getReference('user_' . $j));
            }

            $this->addReference('video_' . $i, $video);

            $manager->persist($video);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            UserFixtures::class,
        ];
    }
}
