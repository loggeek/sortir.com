<?php

namespace App\Service;

use App\Entity\Location;
use App\Repository\LocationRepository;

class LocationService
{
    private LocationRepository $locationRepository;

    public function __construct(LocationRepository $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function createLocationByTown(Location $location): void
    {
        $this->locationRepository->save($location, true);
    }
}
