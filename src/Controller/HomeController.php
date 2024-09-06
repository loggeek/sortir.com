<?php

namespace App\Controller;

use App\DTO\ExcursionFilter;
use App\Entity\Excursion;
use App\Entity\User;
use App\Enum\ExcursionStatus;
use App\Form\ExcursionFilterType;
use App\Repository\CampusRepository;
use App\Repository\ExcursionRepository;
use DateInterval;
use DateTime;
use DateTimeZone;
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
        ExcursionRepository $excursionRepository,
        CampusRepository $campusRepository
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $filter = new ExcursionFilter();
        $filterForm = $this->createForm(ExcursionFilterType::class, $filter, ['user' => $user]);
        $filterForm->handleRequest($request);

        $excursions = $excursionRepository->findAndFilter($user, $filter);
        $this->updateExcursionStatus($excursions);

        return $this->render('home.html.twig', [
            'user' => $user,
            'filterForm' => $filterForm->createView(),
            'excursions' => $excursions,
            'campus' => $campusRepository->findAll()
        ]);
    }

    private function updateExcursionStatus(array $excursions): void
    {
        $now = new DateTime('now', new DateTimeZone('Europe/Paris'));

        /** @var Excursion $e */
        foreach ($excursions as $e) {
            switch ($e->getStatus()) {
                case ExcursionStatus::Open:
                    // toutes les places sont prises OU deadline dépassée
                    if (
                        $e->getParticipants()->count() >= $e->getNbSeats()
                        || $now > $e->getDeadline()
                    ) {
                        $e->setStatus(ExcursionStatus::Closed);
                    }
                    //fallthrough
                case ExcursionStatus::Closed:
                    // il reste des places ET deadline pas encore dépassée
                    if (
                        $e->getParticipants()->count() < $e->getNbSeats()
                        && $now <= $e->getDeadline()
                    ) {
                        $e->setStatus(ExcursionStatus::Open);
                    }
                    // la date de l'excursion est arrivée
                    if ($now >= $e->getDate()) {
                        $e->setStatus(ExcursionStatus::Ongoing);
                    }
                    //fallthrough
                case ExcursionStatus::Ongoing:
                    // la date de FIN de l'excursion est arrivée
                    $endDate = DateTime::createFromInterface($e->getDate())
                        ->add(new DateInterval('PT' . $e->getNbSeats() . 'M')); // cannot fail

                    if ($now > $endDate) {
                        $e->setStatus(ExcursionStatus::Finished);
                    }
                    //fallthrough
                case ExcursionStatus::Finished:
                case ExcursionStatus::Cancelled:
                    // la date de l'archive est arrivée
                    $archivingDate = DateTime::createFromInterface($e->getDate())
                        ->add(new DateInterval('P1M'));

                    if ($now > $archivingDate) {
                        $e->setStatus(ExcursionStatus::Archived);
                    }
                default:
            }
        }
    }
}
