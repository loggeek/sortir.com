<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Town;
use App\Repository\CampusRepository;
use App\Repository\TownRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    /* Villes */

    #[Route('/ville', name: 'ville')]
    public function town(TownRepository $townRepository): Response
    {
        return $this->render('admin/ville.html.twig', [
            'villes' => $townRepository->findAll()
        ]);
    }

    #[Route('/ville/{filter}', name: 'ville_filter')]
    public function filterTown(string $filter, TownRepository $townRepository): Response
    {
        return $this->render('admin/ville.html.twig', [
            'villes' => $townRepository->findAndFilter($filter)
        ]);
    }
    #[Route('/ville/add/{nom}/{code}', name: 'ville_add')]
    public function addTown(string $nom, string $code, TownRepository $villeRepository, EntityManagerInterface $em): Response
    {
        if ($villeRepository->findOneByName($nom)) {
            return $this->redirectToRoute('admin_ville');
        }

        $ville = new Town();
        $ville->setName($nom);
        $ville->setZipcode($code);
        $em->persist($ville);
        $em->flush();

        return $this->redirectToRoute('admin_ville');
    }

    #[Route('/ville/modify/{ancien}/{nom}/{code}', name: 'ville_modify')]
    public function modifyTown(string $ancien, string $nom, string $code, TownRepository $villeRepository, EntityManagerInterface $em): Response
    {
        if ($ancien !== $nom && $villeRepository->findOneByName($nom)) {
            return $this->redirectToRoute('admin_ville');
        }

        $ville = $villeRepository->findOneByName($ancien);

        if ($ville) {
            $ville->setName($nom);
            $ville->setZipcode($code);
            $em->persist($ville);
            $em->flush();
        }

        return $this->redirectToRoute('admin_ville');
    }

    #[Route('/ville/delete/{nom}', name: 'ville_delete')]
    public function deleteville(string $nom, TownRepository $villeRepository, EntityManagerInterface $em): Response
    {
        $ville = $villeRepository->findOneByName($nom);

        if ($ville) {
            $em->remove($ville);
            $em->flush();
        }

        return $this->redirectToRoute('admin_ville');
    }

    /* Campus */

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
        return $this->render('admin/campus.html.twig', [
            'sites' => $campusRepository->findAndFilter($filter)
        ]);
    }

    #[Route('/campus/add/{nom}', name: 'campus_add')]
    public function addCampus(string $nom, CampusRepository $campusRepository, EntityManagerInterface $em): Response
    {
        if ($campusRepository->findOneByName($nom)) {
            return $this->redirectToRoute('admin_campus');
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
        if ($campusRepository->findOneByName($nouveau)) {
            return $this->redirectToRoute('admin_campus');
        }

        $campus = $campusRepository->findOneByName($ancien);

        if ($campus) {
            $campus->setName($nouveau);
            $em->persist($campus);
            $em->flush();
        }

        return $this->redirectToRoute('admin_campus');
    }

    #[Route('/campus/delete/{nom}', name: 'campus_delete')]
    public function deleteCampus(string $nom, CampusRepository $campusRepository, EntityManagerInterface $em): Response
    {
        $campus = $campusRepository->findOneByName($nom);

        if ($campus) {
            $em->remove($campus);
            $em->flush();
        }

        return $this->redirectToRoute('admin_campus');
    }
}
