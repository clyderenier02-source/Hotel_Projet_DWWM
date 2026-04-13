<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Contact;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{
    // 1er test : Contact valide
    // Résultat attendu : le test doit RÉUSSIR car tous les
    // champs sont renseignés et valides.
    public function testContactValide(): void
    {
        $contact = new Contact();
        $contact->setEmail('client@eden-palm.fr');
        $contact->setSubject('Demande de réservation');
        $contact->setMessage('Bonjour, je souhaite réserver une chambre.');
        $contact->setDateSend(new \DateTime('2026-04-09'));

        $this->assertSame('client@eden-palm.fr', $contact->getEmail());
        $this->assertSame('Demande de réservation', $contact->getSubject());
        $this->assertSame('Bonjour, je souhaite réserver une chambre.', $contact->getMessage());
        $this->assertNotNull($contact->getDateSend());
    }

    // 2ème test : Contact invalide sans email
    // Résultat attendu : le test doit RÉUSSIR car on vérifie
    // avec assertFalse que l'email vide n'est pas valide.
    public function testContactSansEmail(): void
    {
        $contact = new Contact();
        $contact->setEmail('');
        $contact->setSubject('Demande de réservation');
        $contact->setMessage('Bonjour, je souhaite réserver une chambre.');
        $contact->setDateSend(new \DateTime('2026-04-09'));

        $emailValide = filter_var($contact->getEmail(), FILTER_VALIDATE_EMAIL) !== false;

        $this->assertFalse($emailValide, "Un email vide ne doit pas être considéré comme valide.");
    }

    // 3ème test : Contact avec email invalide
    // Résultat attendu : le test doit ÉCHOUER car on utilise
    // assertTrue alors que l'email "not-an-email" est invalide.
    // PHPUnit signalera ce test en rouge (Failed) = c'est voulu.
    public function testContactAvecEmailInvalide(): void
    {
        $contact = new Contact();
        $contact->setEmail('not-an-email');
        $contact->setSubject('Demande de réservation');
        $contact->setMessage('Bonjour, je souhaite réserver une chambre.');
        $contact->setDateSend(new \DateTime('2026-04-09'));

        $emailValide = filter_var($contact->getEmail(), FILTER_VALIDATE_EMAIL) !== false;

        // assertTrue sur un email invalide = le test ÉCHOUE volontairement
        $this->assertTrue($emailValide, "Cet email devrait être valide - mais il ne l'est pas.");
    }
}