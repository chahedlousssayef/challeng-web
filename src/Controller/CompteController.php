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
     //Fonction du dépot d'argent dans la banque d'un compte
    #[Route('/compte/depot/{id}', name: 'app_compte_depot', methods: ['POST'])]
    public function depot(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $montant = $request->request->get('montant'); 
        // Validation du montant
        if ($this->isInvalidMontant($montant)) {
            $this->addFlash('error', 'Montant invalide.');
            return $this->redirectToRoute('app_compte');
        }
    
        // Récupération du compte
        $compte = $this->getCompteById($id, $entityManager);
        if (!$compte) {
            $this->addFlash('error', 'Compte introuvable.');
            return $this->redirectToRoute('app_compte');
        }
    
        //  Dépôt sur le compte
        $this->effectuerDepot($compte, $montant, $entityManager);
    
        // Message de succès et redirection
        $this->addFlash('success', 'Montant déposé avec succès !');
        return $this->redirectToRoute('app_compte');
    }
    
    
    // Vérifie si le montant est valide.
    private function isInvalidMontant($montant): bool
    {
        return !$montant || $montant <= 0;
    }
    
    
    // Récupère un compte par son ID
    private function getCompteById(int $id, EntityManagerInterface $entityManager): ?Compte
    {
        return $entityManager->getRepository(Compte::class)->find($id);
    }
    
    
    // Effectue le dépôt sur le compte.
    private function effectuerDepot(Compte $compte, float $montant, EntityManagerInterface $entityManager): void
    {
        $compte->setSolde($compte->getSolde() + $montant); 
        $entityManager->flush();
    }
}
