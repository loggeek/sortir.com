<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginPageTest extends WebTestCase
{
    public function testLoginPage(): void
    {
        // Création d'un client HTTP
        $client = static::createClient();

        // Envoi d'une requête GET vers la page de connexion
        $crawler = $client->request('GET', '/login');

        // Vérifie que la réponse HTTP a un statut de succès (code 2xx)
        $this->assertResponseIsSuccessful();

        // Sélectionne tous les champs de formulaire et le bouton de soumission sur la page (types text, password, checkbox, hidden, et submit)
        $inputs = $crawler->filter('input[type="text"], input[type="password"], input[type="checkbox"], input[type="hidden"], button[type="submit"]');

        // Vérifie que 5 éléments ont été trouvés (4 champs de formulaire et 1 bouton de soumission)
        $this->assertCount(5, $inputs);

        // Vérifie que l'élément <h1> contient bien le texte 'Connectez-vous'
        $this->assertSelectorTextContains('h1', 'Connectez-vous');
    }
}
