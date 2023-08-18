<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    public function testEditProfileFormSubmission(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneById(1);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/fr/profile/1/edit-profile');
        $buttonCrawler = $crawler->selectButton('Sauvegarder');

        $form = $buttonCrawler->form();

        $form['profile[username]'] = 'New username';
        $form['profile[newsletter]'] = '1';

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirection());
        $this->assertEquals('/fr/profile/1', $client->getResponse()->headers->get('Location'));
    }

    public function testProfileAccessDenied(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $admin = $userRepository->findOneById(2);
        $client->loginUser($admin);

        $client->request('GET', '/fr/profile/1/');
        $this->assertTrue($client->getResponse()->isRedirection());
        $this->assertEquals(
            'http://localhost/fr/profile/1',
            $client->getResponse()->headers->get('Location')
        );
    }
}
