<?php

namespace App\Repository;

use App\DTO\ExcursionFilter;
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
        ExcursionFilter $filter,
    ) {
        $qbd = $this->createQueryBuilder('e')
            // Ne pas afficher les sorties en cours de création par d'autres utilisateurs;
            ->andWhere('NOT (e.status = :c_created AND NOT e.organizer = :me)')
            ->setParameter('c_created', ExcursionStatus::Created->value)
            ->setParameter('me', $user->getId());

        if ($filter->getCampus()) {
            $qbd->andWhere('e.campus = :campus')
                ->setParameter('campus', $filter->getCampus()->getId());
        }
        if ($filter->getName()) {
            // Vérifier que la chaîne fournie est contenue dans le nom de la sortie
            $qbd->andWhere('e.name LIKE :name')
                ->setParameter('name', '%'.$filter->getName().'%');
        }
        if ($filter->getDatemin()) {
            $qbd->andWhere('e.date >= :datemin')
                ->setParameter('datemin', $filter->getDatemin()->format('Y-m-d 00:00:00'));
        }
        if ($filter->getDatemax()) {
            $qbd->andWhere('e.date <= :datemax')
                ->setParameter('datemax', $filter->getDatemax()->format('Y-m-d 23:59:59'));
        }
        if ($filter->isOrganizer()) {
            $qbd->andWhere('e.organizer = :organizer')
                ->setParameter('organizer', $user->getId());
        }
        if (!$filter->isShowpast()) {
            $qbd->andWhere('NOT e.status = :c_archives')
                ->setParameter('c_archives', ExcursionStatus::Archived->value);
        }

        $result = $qbd->getQuery()->getResult();

        if ($filter->isParticipating()) {
            $result = array_filter($result, function (Excursion $excursion) use ($user) {
                return $excursion->getParticipants()->contains($user);
            });
        }
        if ($filter->isNonparticipating()) {
            $result = array_filter($result, function (Excursion $excursion) use ($user) {
                return !$excursion->getParticipants()->contains($user);
            });
        }
        usort($result, function (Excursion $a, Excursion $b) {
            return $a->getDate() <=> $b->getDate();
        });

        return $result;
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
