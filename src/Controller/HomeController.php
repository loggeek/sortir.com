<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CampusRepository;
use App\Repository\ExcursionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'app_')]
class HomeController extends AbstractController
{
    #[Route("/", name: "home")]
    public function home(ExcursionRepository $excursionRepository, CampusRepository $campusRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user == null) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('home.html.twig', [
            'user' => $user,
            'excursions' => $excursionRepository->findAll(), // todo filter using form
            'campus' => $campusRepository->findAll()
        ]);
    }
}
