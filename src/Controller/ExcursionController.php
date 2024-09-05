<?php

namespace App\Controller;

use App\Entity\Excursion;
use App\Enum\ExcursionStatus;
use App\Form\ExcursionType;
use App\Repository\TownRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExcursionController extends abstractController
{
    #[Route('/excursion/form', name: 'app_excursion_form')]
    public function create(Request $request, EntityManagerInterface $em, TownRepository $townRepository): Response
    {
        $user = $this->getUser();

        $excursion = new Excursion();
        $excursionForm = $this->createForm(ExcursionType::class, $excursion, ['user' => $user]);

        $towns = $townRepository->findAll();

        $excursionForm->handleRequest($request);

        if ($excursionForm->isSubmitted()) {
            $buttonClicked = $request->request->get('submit');

            if ($excursionForm->isValid()) {

                $excursion = $excursionForm->getData();

                switch ($buttonClicked) {
                    case 'create':
                        $excursion->setStatus(ExcursionStatus::Created);
                        break;
                    case 'publish':
                        $excursion->setStatus(ExcursionStatus::Open);
                        break;
                }

                $excursion->setOrganizer($this->getUser());

                $em->persist($excursion);
                $em->flush();

                $this->addFlash('success', "Sortie créée");
                return $this->redirectToRoute('app_home');
            }
            else{
                $this->addFlash('danger', "Formulaire invalide");
            }
        }

        return $this->render('excursion/form.html.twig', [
            'excursionForm' => $excursionForm->createView(),
            'towns' => $towns,
        ]);

    }

    #[Route('/Excursion/{id}/detail', name: 'app_excursion_detail')]
    public function view($id, EntityManagerInterface $em): Response
    {
        $excursion = $em->getRepository(Excursion::class)->find($id);

        if (!$excursion) {
            throw $this->createNotFoundException('Excursion not found');
        }

        return $this->render('excursion/detail.html.twig', [
            'excursion' => $excursion,
        ]);
    }
}