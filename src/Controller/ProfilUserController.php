<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    function edit(Request $request, EntityManagerInterface $em): Response {
        $user = $this->getUser();
        $form = $this->createForm(ProfilUserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', "Le profil à été mis à jour ");
            return $this->redirectToRoute('app_home');
        }
        return $this->render('profil_user/edit.html.twig', [
            'user' => $user,
            'form' => $form
        ]);
    }
}
