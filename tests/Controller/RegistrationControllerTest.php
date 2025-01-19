<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    // Teste la structure de la page d'inscription
    public function testRegistrationPageStructure(): void
    {
        $client = static::createClient();

        // Charge la page de registration
        $crawler = $client->request('GET', '/register');

        // Vérifie que la réponse HTTP est réussie
        $this->assertResponseIsSuccessful();

        // Vérifie que le header contient les éléments attendus
        $this->assertHeaderExists($crawler);

        // Vérifie que le contenu principal contient un texte attendu
        $this->assertMainContentContains($crawler, 'Register');

        // Vérifie que le footer contient un texte attendu
        $this->assertFooterContains($crawler, 'A propos');
    }

    // Vérifie que le header est présent et contient des liens spécifiques
    private function assertHeaderExists($crawler): void
    {
        $this->assertGreaterThan(0, $crawler->filter('header')->count(), 'Header should be present.');
        $this->assertSelectorTextContains('header', 'Connexion');
        $this->assertSelectorTextContains('header', 'Mon compte');
        $this->assertSelectorTextContains('header', 'Nous Contactez');
    }

    // Vérifie que le contenu principal contient un texte spécifique
    private function assertMainContentContains($crawler, string $expectedText): void
    {
        $this->assertSelectorTextContains('main', $expectedText);
    }

    // Vérifie que le footer contient un texte spécifique
    private function assertFooterContains($crawler, string $expectedText): void
    {
        $this->assertGreaterThan(0, $crawler->filter('footer')->count(), 'Footer should be present.');
        $this->assertSelectorTextContains('footer', $expectedText);
    }
}
