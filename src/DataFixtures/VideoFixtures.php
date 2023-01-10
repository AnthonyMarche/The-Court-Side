<?php

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

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

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $video = new Video();
            $video->setTitle($faker->words(3, true));
            $video->setDescription($faker->paragraphs(1, true));
            $video->setIsPrivate($faker->boolean);
            $video->setNumberOfView($faker->numberBetween(8, 1052));
            $video->setUrl("build/fixturesVideos/Recreated memes ( Then vs Now ) Part 3.mp4");
            $video->setTeaser("build/fixturesVideos/test-video-teaser.mp4");
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[$faker->numberBetween(0, 9)]));
            $video->setCreatedAt($faker->dateTimeBetween('-6 month'));

            $randomUser = rand(0, 1);
            $video->setUser($this->getReference(self::USER[$randomUser]));

            $slug = $this->slugger->slug($video->getTitle());
            $video->setSlug($slug);

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
