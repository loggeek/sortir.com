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
        return $this->render('admin/campus.html.twig', [
            'sites' => $campusRepository->findAndFilter($filter)
        ]);
    }

    #[Route('/campus/add/{nom}', name: 'campus_add')]
    public function addCampus(string $nom, CampusRepository $campusRepository, EntityManagerInterface $em): Response
    {
        if ($campusRepository->findOneByName($nom)) {
            // vérifier que le nouveau nom du campus ne soit pas déjà attribué
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
        $sites = $campusRepository->findAll();

        if ($campusRepository->findOneByName($nouveau)) {
            // vérifier que le nouveau nom du campus ne soit pas déjà attribué
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
