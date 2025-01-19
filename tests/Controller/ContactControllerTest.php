<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{

    // Teste le rendu du template pour la page de contact
    public function testContactPageRendering(): void
    {
        // Crée un client HTTP
        $client = static::createClient();
    
        // Charge la page contact qui utilise base.html.twig
        $crawler = $client->request('GET', '/contact');
    
        // Vérifie que la réponse HTTP est réussie (200 OK)
        $this->assertResponseIsSuccessful();
    
        // Vérifie les éléments du template
        $this->customAssertPageTitleContains($crawler,'Contact');
        $this->assertHeaderExists($crawler);
        $this->assertFooterContains($crawler, 'A propos');
        $this->assertMainContentContains($crawler, 'Comment nous contactez ?');
    }
    
    // Vérifie que le titre de la page contient le texte attendu.
    public function customAssertPageTitleContains($crawler, string $expectedTitle): void
    {
        $this->assertStringContainsString($expectedTitle, $crawler->filter('title')->text());
    }

    // Vérifie que l'en-tête de la page contient les éléments attendus.
    private function assertHeaderExists($crawler): void
    {
        $this->assertGreaterThan(0, $crawler->filter('header')->count(), 'Header should be present.');
        $this->assertSelectorTextContains('header', 'Connexion');
        $this->assertSelectorTextContains('header', 'Mon compte');
        $this->assertSelectorTextContains('header', 'Nous Contactez');
    }
    
    // Vérifie que le footer est présent
    private function assertFooterContains($crawler, string $expectedText): void
    {
        $this->assertGreaterThan(0, $crawler->filter('footer')->count(), 'Footer should be present.');
        $this->assertSelectorTextContains('footer', $expectedText);
    }
    
    // Vérifie que le contenu principal contient le texte attendu.
    private function assertMainContentContains($crawler, string $expectedText): void
    {
        $this->assertSelectorTextContains('main', $expectedText);
    }
}
