<?php
namespace App\Tests;

use App\Entity\Film;
use App\Entity\Platforme;
use App\Tests\Helper\TestEntityFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccessFilmAdminTest extends WebTestCase
{
    public function testAdminFilmNew(): void
    {
        $client = static::createClient();
        // $em = self::getContainer()->get(EntityManagerInterface::class);
        // $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $em = $client->getContainer()->get(EntityManagerInterface::class);
        $hasher = $client->getContainer()->get(UserPasswordHasherInterface::class);


        $user = TestEntityFactory::createAdmin($em,$hasher);
        $client->loginUser($user);

        // Crée un film + plateforme pour le test
        $platform = new Platforme();
        $platform->setName('AddPlatform'.uniqid());
        $em->persist($platform);

        $title='FilmAdd'.uniqid();

        //gestion par easyadmin donc différent du standard
        // $crawler= $client->request('GET','/admin?crudAction=new&crudControllerFqcn=' . urlencode(
        // 'App\Controller\Admin\FilmCrudController'));
        $crawler= $client->request('GET','/admin/film/new');
        $this->assertResponseIsSuccessful(); 

        
        // $form = $crawler->selectButton('Create')->form();
        // $form['Film[title]'] = $title;
        // $form['Film[releaseYear]'] = 2015;
        // $form['Film[platform][]'] = [$platform->getId()]; 

        $form = $crawler->filter('form')->form();
        // $form['ea[newForm][title]'] = $title;
        // $form['ea[newForm][releaseYear]'] = 2015;
        // $form['ea[newForm][platform]'] = [$platform->getId()]; 

        // 6️⃣ Remplissage dynamique des champs en détectant les bons noms
        foreach ($form->all() as $fieldName => $field) {
            if (str_contains($fieldName, '[title]')) {
                $form[$fieldName] = $title;
            } elseif (str_contains($fieldName, '[releaseYear]')) {
                $form[$fieldName] = 2023;
            } elseif (str_contains($fieldName, '[platform]')) {
                $form[$fieldName] = [$platform->getId()]; // ManyToMany
            }
        }



        $client->submit($form);

        // $this->assertTrue($client->getResponse()->isRedirection());
        // 🔍 DEBUG : Voir ce qui se passe après la soumission
        dump($client->getResponse()->getStatusCode());
        //dump($client->getResponse()->getContent());

        // Vérifier les erreurs de formulaire
    $crawler = $client->getCrawler();
    $errors = $crawler->filter('.invalid-feedback, .form-error-message, .error')->each(
        fn($node) => $node->text()
    );
    if (!empty($errors)) {
        echo "\n=== ERREURS DE FORMULAIRE ===\n";
        print_r($errors);
    }

        // Vérifier s'il y a des erreurs de validation
        if (!$client->getResponse()->isRedirection()) {
            $crawler = $client->getCrawler();
            $errors = $crawler->filter('.invalid-feedback, .form-error-message')->each(
                fn($node) => $node->text()
            );
            dump('Erreurs de formulaire:', $errors);
        }

        // Option 1 : Vérifier une redirection
        $this->assertTrue(
            $client->getResponse()->isRedirection(),
            sprintf(
                'Expected redirect but got %d. Response: %s',
                $client->getResponse()->getStatusCode(),
                $client->getResponse()->getContent()
            )
        );

        // Option 2 : Alternative - vérifier le succès (200 ou 3xx)
        // $this->assertResponseIsSuccessful();
        // OU
        // $this->assertResponseStatusCodeSame(302);

        // Rafraîchir l'EntityManager pour voir les nouvelles données
        $em->clear();

        $filmSaved = $em->getRepository(Film::class)->findOneBy(['title' => $title]);
        $this->assertNotNull($filmSaved, 'Le film a été sauvegardé en base');
        $this->assertCount(1, $filmSaved->getPlatform(), 'Le film a bien une plateforme associée');
        $this->assertEquals($platform->getName(), $filmSaved->getPlatform()->first()->getName());
        
        // // Vérifie que le film est en base
        // $filmRepo = $em->getRepository(Film::class);
        // $filmSaved = $filmRepo->findOneBy(['title' => $title]);
        // $this->assertNotNull($filmSaved);

        // // Vérifie que la relation est bien créée
        // $this->assertCount(1, $filmSaved->getPlatform());
        // $this->assertEquals($platform->getName(), $filmSaved->getPlatform()->first()->getName());
    }
}
