<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{
    private const TWELVE_MONTHS_AGO = '-12 months';
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $admin = new User();
        $admin->setEmail('admin@me.fr');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setUsername('admin');
        $admin->setCreatedAt($faker->dateTimeBetween(self::TWELVE_MONTHS_AGO));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsVerified(true);
        $admin->setNewsletter(true);
        $manager->persist($admin);
        $this->addReference('admin', $admin);

        $superAdmin = new User();
        $superAdmin->setEmail('manager@me.fr');
        $superAdmin->setPassword($this->passwordHasher->hashPassword($superAdmin, 'manager'));
        $superAdmin->setUsername('manager');
        $superAdmin->setCreatedAt($faker->dateTimeBetween(self::TWELVE_MONTHS_AGO));
        $superAdmin->setRoles(['ROLE_MEDIA_MANAGER']);
        $superAdmin->setIsVerified(true);
        $superAdmin->setNewsletter(true);
        $manager->persist($superAdmin);
        $this->addReference('superAdmin', $superAdmin);

        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'user'));
            $user->setUsername('user' . $i);
            $user->setCreatedAt($faker->dateTimeBetween(self::TWELVE_MONTHS_AGO));
            $user->setNewsletter(false);
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }
        $manager->flush();
    }
}
