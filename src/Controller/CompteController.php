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
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos comptes.');
        }

        $comptes = $entityManager->getRepository(Compte::class)->findBy(['utilisateur' => $user]);

        return $this->render('compte/compte.html.twig', [
            'controller_name' => 'CompteController',
            'comptes' => $comptes,
        ]);
    }

    #[Route('/compte/new', name: 'app_compte_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour créer un compte.');
        }

        // Vérification de la limite de comptes (max 5 comptes)
        $compteRepository = $entityManager->getRepository(Compte::class);
        $nombreDeComptes = $compteRepository->count(['utilisateur' => $user]);

        if ($nombreDeComptes >= 5) {
            $this->addFlash('error', 'Vous avez atteint la limite de 5 comptes.');
            return $this->redirectToRoute('app_compte');
        }

        $entity = new Compte();
        $entity->setNumero(uniqid('ACC-'));
        $entity->setUtilisateur($user);

        $compteForm = $this->createForm(CompteFormType::class, $entity);
        $compteForm->handleRequest($request);

        if ($compteForm->isSubmitted() && $compteForm->isValid()) {
            // Vérifier si la case épargne est cochée ou non
            $isEpargne = $compteForm->get('type')->getData();
            $entity->setType($isEpargne ? Compte::TYPE_EPARGNE : Compte::TYPE_COURANT);

            $entityManager->persist($entity);
            $entityManager->flush();

            $this->addFlash('success', 'Compte créé avec succès.');

            return $this->redirectToRoute('app_compte');
        }

        return $this->render('compte/new_compte.html.twig', [
            'compteForm' => $compteForm->createView(),
        ]);
    }

    #[Route('/compte/{id}/update', name: 'compte_update')]
    public function update(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $compte = $entityManager->getRepository(Compte::class)->find($id);

        if (!$compte || $compte->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce compte.');
        }

        $compteForm = $this->createForm(CompteFormType::class, $compte);
        $compteForm->handleRequest($request);

        if ($compteForm->isSubmitted() && $compteForm->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Compte mis à jour avec succès.');

            return $this->redirectToRoute('app_compte');
        }

        return $this->render('compte/new_compte.html.twig', [
            'compteForm' => $compteForm->createView(),
            'action' => 'Modifier',
        ]);
    }

    #[Route('/compte/{id}/add-money', name: 'compte_add_money', methods: ['GET', 'POST'])]
    public function addMoney(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $compte = $entityManager->getRepository(Compte::class)->find($id);

        if (!$compte || $compte->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à ce compte.');
        }

        $form = $this->createFormBuilder()
            ->add('montant', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Montant à ajouter',
                'constraints' => [
                    new Positive(['message' => 'Le montant doit être positif.']),
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $montant = $form->getData()['montant'];

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

    #[Route('/compte/{id}/remove-money', name: 'compte_remove_money', methods: ['GET', 'POST'])]
    public function removeMoney(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $compte = $entityManager->getRepository(Compte::class)->find($id);

        if (!$compte || $compte->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à ce compte.');
        }

        $form = $this->createFormBuilder()
            ->add('montant', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Montant à retirer',
                'constraints' => [
                    new Positive(['message' => 'Le montant doit être positif.']),
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $montant = $form->getData()['montant'];

            if ($compte->getSolde() >= $montant) {
                $compte->setSolde($compte->getSolde() - $montant);
                $entityManager->flush();

                $this->addFlash('success', 'Montant retiré avec succès.');
            } else {
                $this->addFlash('error', 'Solde insuffisant pour effectuer ce retrait.');
            }
        }

        return $this->render('compte/remove_money.html.twig', [
            'form' => $form->createView(),
            'compte' => $compte,
        ]);
    }

    #[Route('/compte/{id}/transfer', name: 'compte_transfer', methods: ['GET', 'POST'])]
    public function transfer(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $sourceCompte = $entityManager->getRepository(Compte::class)->find($id);

        if (!$sourceCompte || $sourceCompte->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas transférer à partir de ce compte.');
        }

        $form = $this->createFormBuilder()
            ->add('numero', null, [
                'label' => 'Numéro du compte destinataire',
                'required' => true,
            ])
            ->add('montant', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Montant à transférer',
                'constraints' => [
                    new Positive(['message' => 'Le montant doit être positif.']),
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $destinationCompte = $entityManager->getRepository(Compte::class)->findOneBy(['numero' => $data['numero']]);

            if (!$destinationCompte) {
                $this->addFlash('error', 'Compte destinataire introuvable.');
                return $this->redirectToRoute('compte_transfer', ['id' => $id]);
            }

            $montant = $data['montant'];

            if ($sourceCompte->getSolde() >= $montant) {
                $sourceCompte->setSolde($sourceCompte->getSolde() - $montant);
                $destinationCompte->setSolde($destinationCompte->getSolde() + $montant);
                $entityManager->flush();

                $this->addFlash('success', 'Virement effectué avec succès.');
            } else {
                $this->addFlash('error', 'Solde insuffisant pour effectuer le virement.');
            }
        }

        return $this->render('compte/transfer.html.twig', [
            'form' => $form->createView(),
            'sourceCompte' => $sourceCompte,
        ]);
    }
}
