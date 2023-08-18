<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VideoTest extends WebTestCase
{
    public function testInvalidFilter(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/category/Basketball?sortedBy=test');

        $this->assertResponseStatusCodeSame(400);
    }

    public function testValidFilter(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/category/Basketball?sortedBy=views');

        $this->assertResponseIsSuccessful();
    }
}
