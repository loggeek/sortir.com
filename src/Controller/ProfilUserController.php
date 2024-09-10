<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilUserType;
use App\Repository\CampusRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ProfilUserController extends AbstractController {

    #[Route('/profil/view/{id}', name: 'profile_view')]
    public function show(int $id, UserRepository $userRepository) : Response
    {
        $user = $userRepository->find($id);
        $myprofile = $user === $this->getUser();

        if ($user == null) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('profil_user/view.html.twig', [
            'user' => $user,
            'myprofile' => $myprofile
        ]);
    }

    #[Route('profil/edit', name: 'profil.edit')]
    function edit(Request $request, UserPasswordHasherInterface $passwordHasher, CampusRepository $campusRepository, EntityManagerInterface $em, FileUploader $fileUploader): Response {

        /** @var User $user */
        $user = $this->getUser(); // Récupération de l'utilisateur actuellement connecté
        $originalProfileImage = $user->getProfileImage(); // Sauvegarde l'image actuelle

        $form = $this->createForm(ProfilUserType::class, $user);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {


            $newPassword = $form->get('password')->getData();
            /** @var UploadedFile $file */
            $file = $form->get('profileImage')->getData();


            if($newPassword != null) {
                // Hashage du mot de passe
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            // Gestion de l'image de profil
            if ($file) {
                $newFilename = $fileUploader->upload($file);
                $user->setProfileImage($newFilename);
            } else {
                $user->setProfileImage($originalProfileImage);
            }

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
