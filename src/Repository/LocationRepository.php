<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 */
class LocationRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function save(Location $location, bool $flush = false): void
    {
        $this->_em->persist($location);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Location $location, bool $flush = false): void
    {
        $this->_em->remove($location);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
