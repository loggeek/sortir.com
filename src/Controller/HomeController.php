<?php

namespace App\Controller;

use App\DTO\ExcursionFilter;
use App\Entity\User;
use App\Form\ExcursionFilterType;
use App\Repository\CampusRepository;
use App\Repository\ExcursionRepository;
use App\Service\ExcursionStatusService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'app_')]
class HomeController extends AbstractController
{
    #[Route("/", name: "home")]
    public function home(
        Request $request,
        ExcursionStatusService $excursionStatusService,
        ExcursionRepository $excursionRepository,
        CampusRepository $campusRepository
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $filter = new ExcursionFilter();
        $filterForm = $this->createForm(ExcursionFilterType::class, $filter, ['user' => $user]);
        $filterForm->handleRequest($request);

        $excursions = $excursionRepository->findAndFilter($user, $filter);
        $excursionStatusService->updateExcursionStatus($excursions);

        return $this->render('home.html.twig', [
            'user' => $user,
            'filterForm' => $filterForm->createView(),
            'excursions' => $excursions,
            'campus' => $campusRepository->findAll()
        ]);
    }
}
