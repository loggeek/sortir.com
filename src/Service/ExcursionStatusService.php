<?php

namespace App\Service;

use App\DTO\ExcursionFilter;
use App\Entity\Excursion;
use App\Entity\User;
use App\Enum\ExcursionStatus;
use App\Repository\ExcursionRepository;
use DateInterval;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;

class ExcursionStatusService
{
    private ExcursionRepository $excursionRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ExcursionRepository $excursionRepository, EntityManagerInterface $entityManager)
    {
        $this->excursionRepository = $excursionRepository;
        $this->entityManager = $entityManager;
    }

    public function updateExcursionStatus(array $excursions): void
    {
        $now = new DateTime('now', new DateTimeZone('Europe/Paris'));

        foreach ($excursions as $e) {
            $status = $e->getStatus();
            switch ($status) {
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
            if ($status !== $e->getStatus()) {
                // le statut a été modifié
                $this->entityManager->persist($e);
            }
        }
        $this->entityManager->flush();
    }
}