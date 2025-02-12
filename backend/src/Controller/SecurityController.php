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
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserRepository $userRepository,
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

        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['success' => false, 'message' => 'Identifiants incorrects'], Response::HTTP_UNAUTHORIZED);
        }

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
                'created_at' => $user->getCreatedAt(),
                'updated_at' => $user->getUpdatedAt()
            ]
        ]);
    }

    #[Route(path: '/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse {
        $payload = json_decode($request->getContent(), true);

        $user = new User();
        $user->setFirstname($payload['firstname'] ?? null);
        $user->setLastname($payload['lastname'] ?? null);
        $user->setEmail($payload['email'] ?? null);
        $user->setPhoneNumber($payload['phone_number'] ?? null);
        $user->setAge(isset($payload['age']) ? (int)$payload['age'] : null);
        $user->setAvatar($payload['avatar'] ?? null);
        $user->setActive(true);

        $hashedPassword = $passwordHasher->hashPassword($user, $payload['password'] ?? '');
        $user->setPassword($hashedPassword);

        // Validation
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['success' => false, 'errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
        if ($existingUser) {
            return $this->json(['success' => false, 'message' => 'Cet utilisateur existe déjà']);
        }

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
