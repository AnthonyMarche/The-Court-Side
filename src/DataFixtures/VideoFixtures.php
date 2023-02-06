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

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $videoNumber = 0;

        for ($i = 0; $i < 3; $i++) {
            $video = $this->baseNewVideo();
            $video->setUrl("build/fixturesVideos/basketball_video.mp4");
            $video->setTeaser("build/fixturesVideos/basketball_video.mp4");
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[0]));
            $this->addReference('video_' . $videoNumber, $video);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo();
            $video->setUrl("build/fixturesVideos/basketball_video2.mp4");
            $video->setTeaser("build/fixturesVideos/basketball_video2.mp4");
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[0]));
            $this->addReference('video_' . $videoNumber, $video);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo();
            $video->setUrl("build/fixturesVideos/basketball_video3.mp4");
            $video->setTeaser("build/fixturesVideos/basketball_video3.mp4");
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[0]));
            $this->addReference('video_' . $videoNumber, $video);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo();
            $video->setUrl("build/fixturesVideos/boxe_video.mp4");
            $video->setTeaser("build/fixturesVideos/boxe_video.mp4");
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[1]));
            $this->addReference('video_' . $videoNumber, $video);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo();
            $video->setUrl("build/fixturesVideos/course_video.mp4");
            $video->setTeaser("build/fixturesVideos/course_video.mp4");
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[2]));
            $this->addReference('video_' . $videoNumber, $video);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo();
            $video->setUrl("build/fixturesVideos/escalade_video.mp4");
            $video->setTeaser("build/fixturesVideos/escalade_video.mp4");
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[3]));
            $this->addReference('video_' . $videoNumber, $video);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo();
            $video->setUrl("build/fixturesVideos/escalade_video2.mp4");
            $video->setTeaser("build/fixturesVideos/escalade_video2.mp4");
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[3]));
            $this->addReference('video_' . $videoNumber, $video);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo();
            $video->setUrl("build/fixturesVideos/rugby_video.mp4");
            $video->setTeaser("build/fixturesVideos/rugby_video.mp4");
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[4]));
            $this->addReference('video_' . $videoNumber, $video);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo();
            $video->setUrl("build/fixturesVideos/rugby_video2.mp4");
            $video->setTeaser("build/fixturesVideos/rugby_video2.mp4");
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[4]));
            $this->addReference('video_' . $videoNumber, $video);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo();
            $video->setUrl("build/fixturesVideos/ski_video.mp4");
            $video->setTeaser("build/fixturesVideos/ski_video.mp4");
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[5]));
            $this->addReference('video_' . $videoNumber, $video);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo();
            $video->setUrl("build/fixturesVideos/surf_video.mp4");
            $video->setTeaser("build/fixturesVideos/surf_video.mp4");
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[6]));
            $this->addReference('video_' . $videoNumber, $video);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo();
            $video->setUrl("build/fixturesVideos/surf_video2.mp4");
            $video->setTeaser("build/fixturesVideos/surf_video2.mp4");
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[6]));
            $this->addReference('video_' . $videoNumber, $video);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo();
            $video->setUrl("build/fixturesVideos/tennis_video.mp4");
            $video->setTeaser("build/fixturesVideos/tennis_video.mp4");
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[7]));
            $this->addReference('video_' . $videoNumber, $video);
            $videoNumber++;
            $manager->persist($video);

            $video = $this->baseNewVideo();
            $video->setUrl("build/fixturesVideos/tennis_video2.mp4");
            $video->setTeaser("build/fixturesVideos/tennis_video2.mp4");
            $video->setCategory($this->getReference('category_' . self::CATEGORIES[7]));
            $this->addReference('video_' . $videoNumber, $video);
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

    public function baseNewVideo(): Video
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

        return $video;
    }
}
