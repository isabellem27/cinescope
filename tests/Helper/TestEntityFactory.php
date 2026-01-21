<?php 

namespace App\Tests\Helper;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class TestEntityFactory
{
    public static function createUser(EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        ?string $email = null,
        string $plainPassword ="azerty"
            )
    {
        $user = new Utilisateur();
        $user->setEmail($email ?? ('user_'.uniqid().'@test.local'));
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($hasher->hashPassword($user, $plainPassword));
        $em->persist($user);
        $em->flush();
        return $user;
    }

    public static function createAdmin(EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        ?string $email = null,
        string $plainPassword ="admin"
            )
    {
        $user = new Utilisateur();
        $user->setEmail($email ?? ('user_'.uniqid().'@test.local'));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($hasher->hashPassword($user, $plainPassword));
        $em->persist($user);
        $em->flush();
        return $user;
    }
}