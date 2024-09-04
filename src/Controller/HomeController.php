<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Service\ExcursionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'app_')]
class HomeController extends AbstractController
{
    #[Route("/", name: "home")]
    public function home(ExcursionService $excursionService, CampusRepository $campusRepository): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('home.html.twig', [
            'user' => $this->getUser(),
            'excursions' => $excursionService->afficher(),
            'campus' => $campusRepository->findAll()
        ]);
    }
}
