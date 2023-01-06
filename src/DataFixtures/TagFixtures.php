<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class TagFixtures extends Fixture implements DependentFixtureInterface
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

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

            $slug = $this->slugger->slug($tag->getName());
            $tag->setSlug($slug);

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
