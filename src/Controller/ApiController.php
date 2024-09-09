<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use App\Repository\TownRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends abstractController
{
    #[Route("/api/locations/{townId}", name: "api_locations", methods: ['GET'])]
    public function getLocations(int $townId, LocationRepository $locationRepository, TownRepository $townRepository): JsonResponse
    {
        $town = $townRepository->findOneBy(['id' => $townId]);
        $locations = $locationRepository->findBy(['town' => $townId]);

        $townData = [
            'id' => $town->getId(),
            'name' => $town->getName(),
            'zipcode' => $town->getZipcode(),
        ];

        $locationsData = array_map(function ($location) {
            return [
                'id' => $location->getId(),
                'name' => $location->getName(),
            ];
        }, $locations);

        $data = [
            'town' => $townData,
            'locations' => $locationsData,
        ];
        return new JsonResponse($data);
    }

    #[Route("/api/location/{locationId}", name: "api_location", methods: ['GET'])]
    public function getLocation(int $locationId, LocationRepository $locationRepository): JsonResponse
    {
        $location = $locationRepository->findOneBy(['id' => $locationId]);

        $locationData = [
            'id' => $location->getId(),
            'name' => $location->getName(),
            'address' => $location->getAddress(),
            'longitude' => $location->getLongitude(),
            'latitude' => $location->getLatitude(),
        ];

        $data = [
            'location' => $locationData,
        ];
        return new JsonResponse($data);
    }
}