<?php
namespace App\Tests;

use App\Entity\Film;
use App\Entity\Platforme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AccessFilmPublicTest extends WebTestCase
{
    public function testPublicFilmIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/film');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des films');
    }

    public function testPublicFilmShow(): void
    {
        $client = static::createClient();
        $em = self::getContainer()->get(EntityManagerInterface::class);
        
        // Crée un film + plateforme pour le test
        $platform = new Platforme();
        $platform->setName('TestPlatform'.uniqid());
        $em->persist($platform);

        $film = new Film();
        $film->setTitle('FilmTest'.uniqid());
        $film->setReleaseYear(2023);
        $film->addPlatform($platform);

        $em->persist($film);
        $em->flush();

        $client->request('GET', '/film/'.$film->getId());
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString(
            $platform->getName(),
            $client->getResponse()->getContent()
        );
    }
}
