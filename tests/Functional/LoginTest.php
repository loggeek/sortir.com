<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginTest extends WebTestCase
{
    public function testLoginSuccesful(): void
    {
        // Création d'un client HTTP
        $client = static::createClient();

        // Obtention du générateur d'URL
        /*** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        // Génération de l'URL de la route de connexion
        $crawler = $client->request('GET', $urlGenerator->generate('app_login'));

        // Soumission du formulaire de connexion
        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "Vince",
            "_password" => "111111"
        ]);

        // Soumission du formulaire
        $client->submit($form);

        // Vérification du code de statut de la réponse
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Suivi de la redirection
        $client->followRedirect();

        // Vérification de la route finale
        $this->assertRouteSame('app_home');

    }
}
