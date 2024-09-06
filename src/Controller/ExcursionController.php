<?php

namespace App\Controller;

use App\Entity\Excursion;
use App\Entity\User;
use App\Enum\ExcursionStatus;
use App\Form\ExcursionType;
use App\Repository\LocationRepository;
use App\Repository\TownRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExcursionController extends abstractController
{
    #[Route('/excursion/form', name: 'app_excursion_form')]
    public function create(Request $request, EntityManagerInterface $em, TownRepository $townRepository): Response
    {
        $user = $this->getUser();
        $towns = $townRepository->findAll();

        $excursion = new Excursion();
        $excursionForm = $this->createForm(ExcursionType::class, $excursion, ['user' => $user]);

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
            } else {
                $this->addFlash('danger', "Formulaire invalide");
            }
        }

        return $this->render('excursion/form.html.twig', [
            'excursionForm' => $excursionForm->createView(),
            'towns' => $towns,
        ]);

    }

    #[Route('/excursion/{id}/detail', name: 'app_excursion_detail')]
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

    #[Route('/excursion/{id}/cancel', name: 'app_excursion_cancel')]
    public function cancel($id, EntityManagerInterface $em, Request $request): Response
    {
        $excursion = $em->getRepository(Excursion::class)->find($id);
        if (!$excursion) {
            throw $this->createNotFoundException('Excursion not found');
        }

        $cancelForm = $this->createFormBuilder()
            ->add('motif', TextareaType::class, [
                'label' => 'Motif',
                'required' => true,
                'attr' => ['placeholder' => 'Entrez le motif de votre annulation']
            ])
            ->getForm();

        $cancelForm->handleRequest($request);

        if ($cancelForm->isSubmitted()) {
            if ($cancelForm->isValid()) {
                $motif = $cancelForm->get('motif')->getData();
                $excursion->setCancelReason($motif);
                $excursion->setStatus(ExcursionStatus::Cancelled);
                $em->persist($excursion);
                $em->flush();

                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('excursion/cancel.html.twig', [
            'excursion' => $excursion,
            'cancelForm' => $cancelForm->createView(),
        ]);
    }

    #[Route('/excursion/{id}/publier', name: 'app_excursion_publish')]
    public function publish($id, EntityManagerInterface $em): Response
    {
        $excursion = $em->getRepository(Excursion::class)->find($id);

        if (!$excursion) {
            throw $this->createNotFoundException('Excursion not found');
        } else {
            $excursion->setStatus(ExcursionStatus::Open);
            $em->persist($excursion);
            $em->flush();
        }

        return $this->render('excursion/detail.html.twig', [
            'excursion' => $excursion,
        ]);
    }

    #[Route('/excursion/{id}/inscription', name: 'app_excursion_inscription')]
    public function inscription($id, EntityManagerInterface $em): Response
    {
        $excursion = $em->getRepository(Excursion::class)->find($id);
        /** @var User $user */
        $user = $this->getUser();

        if (!$excursion) {
            throw $this->createNotFoundException('Excursion not found');
        } else {
            $excursion->addParticipant($user);
            $em->persist($excursion);
            $em->flush();
        }

        return $this->render('excursion/detail.html.twig', [
            'excursion' => $excursion,
        ]);
    }

    #[Route('/excursion/{id}/desinscription', name: 'app_excursion_desinscription')]
    public function desinscription($id, EntityManagerInterface $em): Response
    {
        $excursion = $em->getRepository(Excursion::class)->find($id);
        /** @var User $user */
        $user = $this->getUser();

        if (!$excursion) {
            throw $this->createNotFoundException('Excursion not found');
        } else {
            $excursion->removeParticipant($user);
            $em->persist($excursion);
            $em->flush();
        }

        return $this->render('excursion/detail.html.twig', [
            'excursion' => $excursion,
        ]);
    }

    #[Route("/api/locations/{townId}", name: "api_locations", methods: ['GET'])]
    public function getLocations(int $townId, LocationRepository $locationRepository): JsonResponse
    {
        $locations = $locationRepository->findBy(['town' => $townId]);

        $data = array_map(function ($location) {
            return [
                'id' => $location->getId(),
                'name' => $location->getName(),
            ];
        }, $locations);

        return new JsonResponse($data);
    }
}