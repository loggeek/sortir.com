<?php

namespace App\Controller;

use App\Service\ExcursionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'app_')]
class HomeController extends AbstractController
{
    #[Route("/", name: "home")]
    public function home(ExcursionService $excursionService): Response
    {
        return $this->render('home.html.twig', [
            'excursions' => $excursionService->afficher()
        ]);
    }
}
