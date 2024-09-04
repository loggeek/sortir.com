<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Excursion;
use App\Entity\User;
use App\Enum\ExcursionStatus;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Excursion>
 */
class ExcursionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Excursion::class);
    }

    public function findAndFilter(
        User $user, // the currently logged on user
        ?Campus $campus = null,
        ?string $nom = null,
        ?DateTimeInterface $datemin = null,
        ?DateTimeInterface $datemax = null,
        bool $organizer = false,
        bool $participating = false,
        bool $nonparticipating = false,
        bool $showpast = false
    ) {
        $qbd = $this->createQueryBuilder('e');

        if ($campus) {
            $qbd->andWhere('e.campus = :campus')
                ->setParameter('campus', $campus->getId());
        }
        if ($nom) {
            // Vérifier que la chaîne fournie est contenue dans le nom de la sortie
            $qbd->andWhere('e.nom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        }
        if ($datemin) {
            $qbd->andWhere('e.date >= :datemin')
                ->setParameter('datemin', $datemin->format('Y-m-d'));
        }
        if ($datemax) {
            $qbd->andWhere('e.date <= :datemax')
                ->setParameter('datemax', $datemax->format('Y-m-d'));
        }
        if ($organizer) {
            $qbd->andWhere('e.organizer = :organizer')
                ->setParameter('organizer', $user->getId());
        }

        // TODO participation
        /*
        if ($participating) {
            $sorties = array_filter($sorties, fn ($sortie) => in_array($user, $sortie->getParticipants()));
        }
        if ($nonparticipating) {
            $sorties = array_filter($sorties, fn ($sortie) => !in_array($user, $sortie->getParticipants()));
        }
        */

        if (!$showpast) {
            $qbd->andWhere('NOT (e.status = :c_finished OR NOT e.status = :c_archived) ')
                ->setParameter('c_finished', ExcursionStatus::Finished)
                ->setParameter('c_archived', ExcursionStatus::Archived);
        }

        return $qbd->getQuery()->getResult();
    }

//    /**
//     * @return Excursion[] Returns an array of Excursion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Excursion
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
