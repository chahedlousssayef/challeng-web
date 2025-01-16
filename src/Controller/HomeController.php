<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// ...

class HomeController extends AbstractController
{
    #[Route('/template/home/index.html.twig')]
    public function index(): Response
    {
        $text = "BankShop";

        return $this->render('home/index.html.twig', [
            'text' => $text,
        ]);
    }
}
