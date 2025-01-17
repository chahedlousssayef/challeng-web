<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Form\CompteFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CompteController extends AbstractController
{
    #[Route('/compte', name: 'app_compte')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $Comptes = $entityManager->getRepository(Compte::class)->findAll();
        return $this->render('compte/compte.html.twig', [
            'controller_name' => 'CompteController',
            'Comptes' => $Comptes,
        ]);
    }

    #[Route('/compte/new', name: 'app_compte_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = new Compte();
        $Compteform = $this->createForm(CompteFormType::class, $entity);
        $Compteform->handleRequest($request);

        if ($Compteform->isSubmitted() && $Compteform->isValid()) {
            $entityManager->persist($entity);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('compte/new_compte.html.twig', [
            'Compteform' => $Compteform->createView(),
        ]);
    }

    #[Route('/compte/{id}/update', name: 'compte_update')]
    public function update(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $entity = $em->getRepository(Compte::class)->find($id);
    
        if (!$entity) {
            throw $this->createNotFoundException('Compte non trouvé');
        }
    
        $Compteform = $this->createForm(CompteFormType::class, $entity);
        $Compteform->handleRequest($request);
    
        if ($Compteform->isSubmitted() && $Compteform->isValid()) {
            $em->persist($entity);
            $em->flush();
    
            return $this->redirectToRoute('app_compte');
        }
    
        return $this->render('compte/new_compte.html.twig', [
            'Compteform' => $Compteform->createView(),
            'action' => 'Modifier',
        ]);
    }
    

}
