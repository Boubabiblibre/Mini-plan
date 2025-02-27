<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Repository\UsersRepository;
use App\Entity\Users;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['POST'])]
    public function login(
        Request $request,
        UsersRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {
        $payload = json_decode($request->getContent(), true);

        $email = $payload['email'] ?? null;
        $password = $payload['password'] ?? null;

        if (!$email || !$password) {
            return $this->json(['success' => false, 'message' => 'Email et mot de passe requis'], Response::HTTP_BAD_REQUEST);
        }

        // Rechercher l'utilisateur par email
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user instanceof Users || !$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['success' => false, 'message' => 'Identifiants incorrects'], Response::HTTP_UNAUTHORIZED);
        }

        // Vérification des rôles de l'utilisateur
        //dump($user->getRoles()); exit;

        // Générer un token JWT
        $token = $jwtManager->create($user);

        return $this->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
                'phone_number' => $user->getPhoneNumber(),
                'age' => $user->getAge(),
                'avatar' => $user->getAvatar(),
                'is_active' => $user->isActive(),
                // 'created_at' => $user->getCreatedAt(),
                // 'updated_at' => $user->getUpdatedAt()
                'created_at' => $user->getCreatedAt()->format('Y-m-d\TH:i:sP'), // ISO 8601
                'updated_at' => $user->getUpdatedAt()->format('Y-m-d\TH:i:sP')  // ISO 8601
            ]
        ]);
    }
}
