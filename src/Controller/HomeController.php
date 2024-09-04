<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        if ($this->getUser() == null){
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        return $this->render('home/home.html.twig', ['user' => $user]);
    }
}