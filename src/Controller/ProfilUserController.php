<?php

namespace App\Controller;

use App\Form\ProfilUserType;
use App\Repository\CampusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilUserController extends AbstractController {
    #[Route('/profil/user', name: 'profil_user.index')]
    public function index(): Response
    {
        return $this->render('profil_user/index.html.twig', [
            'controller_name' => 'ProfilUserController',
        ]);
    }

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
    function edit(Request $request, UserPasswordHasherInterface $passwordHasher, CampusRepository $campusRepository, EntityManagerInterface $em, SluggerInterface $slugger): Response {

        $user = $this->getUser(); // Récupération de l'utilisateur actuellement connecté
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

            if($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                // Déplacez le fichier dans un répertoire dédié
                    $file->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/profile_images',
                        $newFilename
                    );
                } catch (FileException $e) {}

                $user->setProfileImage($newFilename);
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
