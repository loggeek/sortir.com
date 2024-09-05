<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilUserType;
use App\Repository\CampusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ProfilUserController extends AbstractController {
    #[Route('/profil/user', name: 'profil_user.index')]
    public function index(): Response
    {
        return $this->render('profil_user/index.html.twig', [
            'controller_name' => 'ProfilUserController',
        ]);
    }

//    Afficher les détails partiels d'un organisateur
    #[Route('/profil/view', 'profil.view')]
    public function show() : Response {

        $user = $this->getUser();
        return $this->render('profil_user/view.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('profil/edit', name: 'profil.edit')]
    function edit(Request $request, UserPasswordHasherInterface $passwordHasher, CampusRepository $campusRepository, EntityManagerInterface $em): Response {

        $user = $this->getUser(); // Récupération de l'utilisateur actuellement connecté

        $form = $this->createForm(ProfilUserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('plainPassword')->getData();

            // Hashage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "Le profil à été mis à jour ");

            return $this->redirectToRoute('app_home');
        }
        return $this->render('profil_user/edit.html.twig', [
            'user' => $user,
            'campus' => $campusRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }
}
