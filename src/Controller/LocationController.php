<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationFormType;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LocationController extends AbstractController
{
    #[Route('/location/new', name: 'app_location_new')]
    public function new(Request $req, EntityManagerInterface $em): Response
    {
        $location = new Location();
        $form = $this->createForm(LocationFormType::class, $location);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($location);
            $em->flush();
            return  $this->redirectToRoute('app_excursion_form');
        }

        return $this->render('location/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/location/by-town/{townId}', name: 'location_by_town')]
    public  function getLocationByTown(int $townId, LocationRepository $locationRepository) : Response {
        $locations =$locationRepository->findBy(['town' => $townId]);

        return $this->json($locations);
    }


}
