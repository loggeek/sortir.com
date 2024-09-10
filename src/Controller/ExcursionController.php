<?php

namespace App\Controller;

use App\Entity\Excursion;
use App\Entity\User;
use App\Enum\ExcursionStatus;
use App\Form\ExcursionCancelType;
use App\Form\ExcursionModifyType;
use App\Form\ExcursionType;
use App\Repository\LocationRepository;
use App\Repository\TownRepository;
use App\Security\Voter\ExcursionVoter;
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
                        $message = 'Sortie créée';
                        break;
                    case 'publish':
                        $excursion->setStatus(ExcursionStatus::Open);
                        $message = 'Sortie publiée';
                        break;
                }

                $excursion->setOrganizer($this->getUser());
                $excursion->addParticipant($this->getUser());
                $em->persist($excursion);
                $em->flush();

                $this->addFlash('success', $message);
                return $this->redirectToRoute('app_home');
            } else {
                $this->addFlash('danger', "Formulaire invalide");
            }
        }

        return $this->render('excursion/form.html.twig', [
            'excursionForm' => $excursionForm->createView(),
            'towns' => $towns,
            'modifie' => false,
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
        if (!$excursion) throw $this->createNotFoundException('Excursion not found');
        $this->denyAccessUnlessGranted(ExcursionVoter::CANCEL, $excursion);

        $cancelForm = $this->createForm(ExcursionCancelType::class, $excursion);

        $cancelForm->handleRequest($request);

        if ($cancelForm->isSubmitted() && $cancelForm->isValid()) {
            $excursion = $cancelForm->getData();
            $excursion->setStatus(ExcursionStatus::Cancelled);
            $em->persist($excursion);
            $em->flush();

            $this->addFlash('success', 'Sortie annulée');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('excursion/cancel.html.twig', ['excursion' => $excursion,
            'cancelForm' => $cancelForm->createView(),]);
    }

    #[Route('/excursion/{id}/publier', name: 'app_excursion_publish')]
    public function publish($id, EntityManagerInterface $em): Response
    {
        $excursion = $em->getRepository(Excursion::class)->find($id);
        $this->denyAccessUnlessGranted(ExcursionVoter::PUBLISH, $excursion);

        if (!$excursion) {
            throw $this->createNotFoundException('Excursion not found');
        } else {
            $excursion->setStatus(ExcursionStatus::Open);
            $em->persist($excursion);
            $em->flush();
        }

        $this->addFlash('success', 'Sortie publiée');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/excursion/{id}/inscription', name: 'app_excursion_inscription')]
    public function inscription($id, EntityManagerInterface $em): Response
    {
        $excursion = $em->getRepository(Excursion::class)->find($id);
        $this->denyAccessUnlessGranted(ExcursionVoter::REGISTER, $excursion);

        /** @var User $user */
        $user = $this->getUser();

        if (!$excursion) {
            throw $this->createNotFoundException('Excursion not found');
        } else {
            $excursion->addParticipant($user);
            $em->persist($excursion);
            $em->flush();
        }

        $this->addFlash('success', 'Vous êtes inscrit(e)');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/excursion/{id}/desinscription', name: 'app_excursion_desinscription')]
    public function desinscription($id, EntityManagerInterface $em): Response
    {
        $excursion = $em->getRepository(Excursion::class)->find($id);
        $this->denyAccessUnlessGranted(ExcursionVoter::UNREGISTER, $excursion);
        /** @var User $user */
        $user = $this->getUser();

        if (!$excursion) {
            throw $this->createNotFoundException('Excursion not found');
        } else {
            $excursion->removeParticipant($user);
            $em->persist($excursion);
            $em->flush();
        }

        $this->addFlash('success', 'Vous êtes désinscrit(e)');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/excursion/{id}/modifier', name: 'app_excursion_modifier')]
    public function modifier($id, Request $request, EntityManagerInterface $em, TownRepository $townRepository, LocationRepository $locationRepository): Response
    {
        $user = $this->getUser();
        $towns = $townRepository->findAll();

        $excursion = $em->getRepository(Excursion::class)->find($id);
        $this->denyAccessUnlessGranted(ExcursionVoter::EDIT, $excursion);
        $excursionForm = $this->createForm(ExcursionModifyType::class, $excursion, ['user' => $user]);

        $location = $locationRepository->find($excursion->getLocation()->getId());

        $excursionForm->handleRequest($request);

        if ($excursionForm->isSubmitted()) {
            $buttonClicked = $request->request->get('submit');

            if ($excursionForm->isValid()) {

                $excursion = $excursionForm->getData();

                switch ($buttonClicked) {
                    case 'create':
                        $excursion->setStatus(ExcursionStatus::Created);
                        $message = 'Sortie modifiée';
                        break;
                    case 'publish':
                        $excursion->setStatus(ExcursionStatus::Open);
                        $message = 'Sortie publiée';
                        break;
                    case 'delete':
                        $em->remove($excursion);
                        $em->flush();
                        $this->addFlash('success', 'Sortie supprimée');
                        return $this->redirectToRoute('app_home');
                }

                $excursion->setOrganizer($this->getUser());
                $excursion->addParticipant($this->getUser());
                $em->persist($excursion);
                $em->flush();

                $this->addFlash('success', $message);
                return $this->redirectToRoute('app_home');
            } else {
                $this->addFlash('danger', "Formulaire invalide");
            }
        }

        return $this->render('excursion/form.html.twig', [
            'excursionForm' => $excursionForm->createView(),
            'towns' => $towns,
            'location' => $location,
            'modifie' => true,
        ]);
    }
}