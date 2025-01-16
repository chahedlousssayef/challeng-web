<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseTemplateTest extends WebTestCase
{

    // Teste le rendu du template de base
    public function testBaseTemplateRendering(): void
    {
        // Crée un client HTTP
        $client = static::createClient();
    
        // Charge la page racine qui utilise base.html.twig
        $crawler = $client->request('GET', '/');
    
        // Vérifie que la réponse HTTP est réussie (200 OK)
        $this->assertResponseIsSuccessful();
    
        // Vérifie les éléments du template
        $this->customAssertPageTitleContains($crawler,'BankShop');
        $this->assertHeaderExists($crawler);
        $this->assertFooterContains($crawler, 'A propos');
        $this->assertMainContentContains($crawler, 'maintenant');
    }
    


    // Vérifie que le titre de la page contient le texte attendu.
    public function customAssertPageTitleContains($crawler, string $expectedTitle): void
    {
        // Vérifie que le titre de la page contient la valeur attendue
        $this->assertStringContainsString($expectedTitle, $crawler->filter('title')->text());
    }
    


    //Vérifie que l'en-tête de la page contient les éléments attendus.
    private function assertHeaderExists($crawler): void
    {
        $this->assertGreaterThan(0, $crawler->filter('header')->count(), 'Header should be present.');
        $this->assertSelectorTextContains('header', 'Connexion');
        $this->assertSelectorTextContains('header', 'Mon compte');
        $this->assertSelectorTextContains('header', 'Nous Contactez');
    }
    
    

     //Vérifie que le footer contient un lien vers "Service".
    
    private function assertFooterContains($crawler, string $expectedText): void
    {
        $this->assertGreaterThan(0, $crawler->filter('footer')->count(), 'Footer should be present.');
        $this->assertSelectorTextContains('footer', $expectedText);
    }
    

    
     //Vérifie que le contenu principal contient le texte attendu.
    private function assertMainContentContains($crawler, string $expectedText): void
    {
        // On vérifie le contenu dans la balise <main>
        $this->assertSelectorTextContains('main', $expectedText);
    }
}    