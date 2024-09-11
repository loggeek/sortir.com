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

        $inputs = $crawler->filter('input[type="text"], input[type="password"], input[type="checkbox"], input[type="hidden"], button[type="submit"]');
        $this->assertCount(5, $inputs);

        $this->assertSelectorTextContains('h1', 'Connectez-vous');
    }
}
