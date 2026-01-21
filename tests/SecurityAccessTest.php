<?php

namespace App\Tests;

use App\Tests\Helper\TestEntityFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityAccessTest extends WebTestCase
{

    public function testNonLoggedUserRedirectedToLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/film');

        // Vérifie le code HTTP
        $this->assertResponseStatusCodeSame(302);

        // Vérifie la cible de la redirection
        $this->assertResponseRedirects('/login');

        // Autre façon de faire
        // $this->assertResponseRedirects(
        //     '/login',
        //     302
        // );
    }

    public function testUserRoleUserForbidden(): void
    {
        $client = static::createClient();
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        
        // Creation d'un user de test
        $user = TestEntityFactory::createUser($em,$hasher);
        //login
        $client->loginUser($user);
        $client->request('GET', '/admin/film');

        $this->assertResponseStatusCodeSame(403);        
    }

    public function testAdminRoleAccessOk(): void
    {
        $client = static::createClient();
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);

        $user = TestEntityFactory::createAdmin($em,$hasher);
        $client->loginUser($user);

        $client->request('GET', '/admin/film');
        $this->assertResponseIsSuccessful();
    }
}
