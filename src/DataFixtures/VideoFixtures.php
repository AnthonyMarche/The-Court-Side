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
        'Basketball',
        'Boxe',
        'Course',
        'Escalade',
        'Rugby',
        'Ski',
        'Surf',
        'Tennis',
    ];

    public const USER = [
        'admin',
        'superAdmin'
    ];

    public const VIDEO_PATH = "build/fixturesVideos/";

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $videoNumber = 0;

        for ($i = 0; $i < 3; $i++) {
            $video = $this->baseNewVideo(self::VIDEO_PATH . "basketball_video.mp4", self::CATEGORIES[0], $videoNumber);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo(self::VIDEO_PATH . "basketball_video2.mp4", self::CATEGORIES[0], $videoNumber);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo(self::VIDEO_PATH . "basketball_video3.mp4", self::CATEGORIES[0], $videoNumber);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo(self::VIDEO_PATH . "boxe_video.mp4", self::CATEGORIES[1], $videoNumber);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo(self::VIDEO_PATH . "course_video.mp4", self::CATEGORIES[2], $videoNumber);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo(self::VIDEO_PATH . "escalade_video.mp4", self::CATEGORIES[3], $videoNumber);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo(self::VIDEO_PATH . "escalade_video2.mp4", self::CATEGORIES[3], $videoNumber);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo(self::VIDEO_PATH . "rugby_video.mp4", self::CATEGORIES[4], $videoNumber);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo(self::VIDEO_PATH . "rugby_video2.mp4", self::CATEGORIES[4], $videoNumber);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo(self::VIDEO_PATH . "ski_video.mp4", self::CATEGORIES[5], $videoNumber);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo(self::VIDEO_PATH . "surf_video.mp4", self::CATEGORIES[6], $videoNumber);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo(self::VIDEO_PATH . "surf_video2.mp4", self::CATEGORIES[6], $videoNumber);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo(self::VIDEO_PATH . "tennis_video.mp4", self::CATEGORIES[7], $videoNumber);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo(self::VIDEO_PATH . "tennis_video2.mp4", self::CATEGORIES[7], $videoNumber);
            $videoNumber++;
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

    public function baseNewVideo(string $url, string $category, int $videoNumber): Video
    {
        $faker = Factory::create();

        $video = new Video();
        $video->setTitle($faker->words(3, true));
        $video->setDescription($faker->paragraphs(1, true));
        $video->setIsPrivate($faker->boolean);
        $video->setNumberOfView($faker->numberBetween(8, 1052));
        $video->setNumberOfLike($faker->numberBetween(0, 100));
        $video->setCreatedAt($faker->dateTimeBetween('-6 month'));

        $randomUser = rand(0, 1);
        $video->setUser($this->getReference(self::USER[$randomUser]));

        $slug = $this->slugger->slug($video->getTitle());
        $video->setSlug($slug);

        $video->setUrl($url);
        $video->setCategory($this->getReference('category_' . $category));
        $this->addReference('video_' . $videoNumber, $video);

        return $video;
    }
}
