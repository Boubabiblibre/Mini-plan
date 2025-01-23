<?php

namespace App\Controller;  
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository; 
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, UserRepository $userRepository
    ): JsonResponse {
        $firstname = $request->request->get('firstname');
        $password = $request->request->get('password');

        // Rechercher l'utilisateur
        $user = $userRepository->findOneBy(['firstname' => $firstname]);

        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Utilisateur non trouvé']);
        }
        // Soit on return un JWT encrytper un token
        // Soit pour l'instant on renvoie juste true
        
        return $this->json(["success" => True]);
        
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(
        Request $request, 
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $firstname = $request->request->get('firstname');
        $password = $request->request->get('password');

        if (!$firstname || !$password) {
            return $this->json(['success' => false, 'message' => 'Veuillez fournir un prénom et un mot de passe']);
        }

        // Vérifier si l'utilisateur existe déjà
        $userRepository = $entityManager->getRepository(User::class);
        $existingUser = $userRepository->findOneBy(['firstname' => $firstname]);

        if ($existingUser) {
            return $this->json(['success' => false, 'message' => 'Cet utilisateur existe déjà']);
        }

        // Créer un nouvel utilisateur
        $user = new User();
        $user->setFirstname($firstname);
        
        // Hasher le mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        // Sauvegarder l'utilisateur
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['success' => true, 'message' => 'Utilisateur enregistré avec succès']);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


}
