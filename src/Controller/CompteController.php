<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Form\CompteFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

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

    #[Route('/compte/{id}/add-money', name: 'compte_add_money', methods: ['GET', 'POST'])]
    public function addMoney(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $compte = $entityManager->getRepository(Compte::class)->find($id);

        if (!$compte) {
            throw $this->createNotFoundException('Compte non trouvé.');
        }

        // Création d'un formulaire simple pour ajouter de l'argent
        $form = $this->createFormBuilder()
            ->add('montant', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Montant à ajouter',
                'constraints' => [
                    new Positive([
                        'message' => 'Le montant doit être positif.',
                    ]),
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $montant = $data['montant'];

            // Ajouter le montant au solde du compte
            $compte->setSolde($compte->getSolde() + $montant);

            $entityManager->flush();

            $this->addFlash('success', 'Montant ajouté avec succès.');

            return $this->redirectToRoute('app_compte');
        }

        return $this->render('compte/add_money.html.twig', [
            'form' => $form->createView(),
            'compte' => $compte,
        ]);
    }
}
