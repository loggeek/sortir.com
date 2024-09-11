<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginPageTest extends WebTestCase
{
    public function testLoginPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();

        $inputs = $crawler->filter('input');
        $this->assertCount(4, $inputs);

        $button = $crawler->filter('button');
        $this->assertCount(1, $button);

        $this->assertSelectorTextContains('h1', 'Connectez-vous');
    }
}
