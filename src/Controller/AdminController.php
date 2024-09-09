<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    // TODO: ville

    #[Route('/campus', name: 'campus')]
    public function campus(CampusRepository $campusRepository): Response
    {
        return $this->render('admin/campus.html.twig', [
            'sites' => $campusRepository->findAll()
        ]);
    }

    #[Route('/campus/{filter}', name: 'campus_filter')]
    public function filterCampus(string $filter, CampusRepository $campusRepository): Response
    {
        $sites = $campusRepository->findAll();
        $sites = array_filter($sites,
            fn ($campus) => str_contains(strtoupper($campus->getName()), strtoupper($filter))
        );

        return $this->render('admin/campus.html.twig', [
            'sites' => $sites
        ]);
    }

    #[Route('/campus/add/{nom}', name: 'campus_add')]
    public function addCampus(string $nom, CampusRepository $campusRepository, EntityManagerInterface $em): Response
    {
        foreach ($campusRepository->findAll() as $campus) {
            if ($campus->getName() === $nom) {
                // TODO: avertir que le campus existe déjà
                return $this->redirectToRoute('admin_campus');
            }
        }

        $campus = new Campus();
        $campus->setName($nom);
        $em->persist($campus);
        $em->flush();

        return $this->redirectToRoute('admin_campus');
    }


    #[Route('/campus/modify/{ancien}/{nouveau}', name: 'campus_modify')]
    public function modifyCampus(string $ancien, string $nouveau, CampusRepository $campusRepository, EntityManagerInterface $em): Response
    {
        $sites = $campusRepository->findAll();

        // vérifier que le nouveau nom du campus ne soit pas déjà attribué
        foreach ($campusRepository->findAll() as $campus) {
            if ($campus->getName() === $nouveau) {
                // TODO: avertir que le campus existe déjà
                return $this->redirectToRoute('admin_campus');
            }
        }

        // trouver le campus à modifier
        // c'est moche mais ça fait l'affaire - bouger ça dans le service ?
        $campus = (function() use ($sites, $ancien) {
            foreach ($sites as $campus) {
                if ($campus->getName() === $ancien) {
                    return $campus;
                }
            }
            return null;
        })();

        // vérifier qu'il existe bel et bien
        if ($campus == null) {
            // TODO: avertir que le campus n'existe pas
            return $this->redirectToRoute('admin_campus');
        }

        // modifier le campus
        $campus->setName($nouveau);
        $em->persist($campus);
        $em->flush();

        return $this->redirectToRoute('admin_campus');
    }
}
