<?php

namespace App\DataFixtures;

use App\Entity\Users;
use App\Entity\Category;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 🔹 Créer un utilisateur admin
        $user = new Users();
        $user->setFirstname('Admin');
        $user->setLastname('User');
        $user->setEmail('admin@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setAvatar('default.png');
        $user->setIsActive(true);
        $manager->persist($user);

        // 🔹 Ajouter une catégorie
        $category = new Category();
        $category->setName('Streaming');
        $category->setDescription('Services de streaming comme Netflix, Disney+');
        $category->setColor('#0000FF');
        $category->setType('expense'); 
        $category->setUser($user);
        $manager->persist($category);

        // 🔹 Ajouter un service
        $service = new Service();
        $service->setName('Netflix');
        $service->setDescription('Service de streaming de films et séries');
        $service->setProvider('Netflix Inc.');
        $service->setLogo('netflix.png');
        $service->setWebsite('https://www.netflix.com');
        $service->setCategory($category);
        $manager->persist($service);

        // 🚀 Appliquer les changements en base de données
        $manager->flush();
    }
}
