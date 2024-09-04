<?php

namespace App\Controller;

use App\Entity\Excursion;
use App\Enum\ExcursionStatus;
use App\Form\ExcursionType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExcursionController extends abstractController
{
    #[Route('/Excursion/form', name: 'app_excursion_form')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {

        $excursion = new Excursion();
        $excursionForm = $this->createForm(ExcursionType::class, $excursion);

//        var_dump($excursion);
        $excursionForm->handleRequest($request);

        if ($excursionForm->isSubmitted()) {

            $excursion = $excursionForm->getData();
            $excursion->setStatus(ExcursionStatus::Created);
            $excursion->setOrganizer($this->getUser());

//            dd($excursion);
            $em->persist($excursion);
            $em->flush();

//            return $this->redirectToRoute('app_excursion_show', ['id' => $excursion->getId()]);
            return $this->redirectToRoute('home');
        }

        return $this->render('excursion/form.html.twig', [
            'excursionForm' => $excursionForm->createView()
        ]);

    }


}