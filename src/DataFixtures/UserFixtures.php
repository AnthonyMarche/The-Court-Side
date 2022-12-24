<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{
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
        $admin->setCreatedAt($faker->dateTimeBetween('-6 month'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $this->addReference('user_2', $admin);


        $superAdmin = new User();
        $superAdmin->setEmail('superadmin@me.fr');
        $superAdmin->setPassword($this->passwordHasher->hashPassword($superAdmin, 'superadmin'));
        $superAdmin->setUsername('superadmin');
        $superAdmin->setCreatedAt($faker->dateTimeBetween('-6 month'));
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $manager->persist($superAdmin);
        $this->addReference('user_1', $superAdmin);


        for ($i = 0; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'user'));
            $user->setUsername('user' . $i);
            $user->setCreatedAt($faker->dateTimeBetween('-6 month'));
            $manager->persist($user);
        }
        $manager->flush();
    }
}
