<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    // TODO: ville

    #[Route('/campus/', name: 'campus')]
    public function campus(CampusRepository $campusRepository): Response
    {
        return $this->render('admin/campus.html.twig', [
            'sites' => $campusRepository->findAll()
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
}
