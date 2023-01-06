<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture
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

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        foreach (self::CATEGORIES as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $category->setCreatedAt($faker->dateTimeBetween('-6 month'));

            $slug = $this->slugger->slug($category->getName());
            $category->setSlug($slug);

            $manager->persist($category);
            $this->addReference('category_' . $categoryName, $category);
        }
        $manager->flush();
    }
}
