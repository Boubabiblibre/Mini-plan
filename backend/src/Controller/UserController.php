<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;

#[Route('/api/user')]
/**
 * @OA\Tag(name="User")
 */
class UserController extends AbstractController
{
    /**
     * @OA\Post(
     *     path="/api/user/create",
     *     summary="Créer un nouvel utilisateur",
     *     description="Crée un nouvel utilisateur avec un email unique et un mot de passe sécurisé.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"firstname", "lastname", "email", "password"},
     *             @OA\Property(property="firstname", type="string", example="John"),
     *             @OA\Property(property="lastname", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="strongpassword"),
     *             @OA\Property(property="phone_number", type="string", example="0606060606"),
     *             @OA\Property(property="avatar", type="string", example="avatar.png")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Utilisateur créé avec succès"),
     *             @OA\Property(property="user_id", type="string", example="c9b1a3d2-ff98-4a6f-9832-bbdb2300efdf")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Utilisateur déjà existant"
     *     )
     * )
     */
    #[Route('/create', name: 'create_user', methods: ['POST'])]
    public function createUser(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Vérification des champs obligatoires
        if (!isset($data['firstname'], $data['lastname'], $data['email'], $data['password'])) {
            return $this->json([
                'success' => false,
                'message' => 'Tous les champs requis doivent être renseignés'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Vérification si l'utilisateur existe déjà
        $existingUser = $entityManager->getRepository(Users::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json([
                'success' => false,
                'message' => 'Cet utilisateur existe déjà'
            ], Response::HTTP_CONFLICT);
        }

        // Création de l'utilisateur
        $user = new Users();
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setEmail($data['email']);
        $user->setPhoneNumber($data['phone_number'] ?? null);
        $user->setAge(isset($data['age']) ? (int) $data['age'] : null);
        $user->setAvatar($data['avatar'] ?? 'default.png');
        $user->setIsActive(true);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        // Hashage du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Validation des données
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json([
                'success' => false,
                'errors' => $errorMessages
            ], Response::HTTP_BAD_REQUEST);
        }

        // Sauvegarde en base de données
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès',
            'user_id' => $user->getId(),
        ], Response::HTTP_CREATED);
    }
    /**
     * @OA\Get(
     *     path="/api/user/all",
     *     summary="Récupérer tous les utilisateurs",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des utilisateurs"
     *     )
     * )
     */
    // 🔹 2. Récupérer tous les utilisateurs
    #[Route('/all', name: 'get_all_users', methods: ['GET'])]
    public function getAllUsers(EntityManagerInterface $entityManager): JsonResponse
    {
        $users = $entityManager->getRepository(Users::class)->findAll();
        $userData = array_map(function (Users $user) {
            return [
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
                'phone_number' => $user->getPhoneNumber(),
                'avatar' => $user->getAvatar(),
                'is_active' => $user->isActive(),
            ];
        }, $users);

        return $this->json($userData);
    }
/**
     * @OA\Get(
     *     path="/api/user/{id}",
     *     summary="Récupérer un utilisateur par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de l'utilisateur"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé"
     *     )
     * )
     */
    // 🔹 3. Récupérer un utilisateur par ID
    #[Route('/{id}', name: 'get_user_by_id', methods: ['GET'])]
    public function getUserById(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(Users::class)->find($id);
        if (!$user) {
            return $this->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        return $this->json([
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'phone_number' => $user->getPhoneNumber(),
            'avatar' => $user->getAvatar(),
            'is_active' => $user->isActive(),
        ]);
    }
     /**
     * @OA\Delete(
     *     path="/api/user/delete/{id}",
     *     summary="Supprimer un utilisateur",
     *     description="Supprime un utilisateur existant en fonction de son ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur à supprimer",
     *         @OA\Schema(type="string", example="c9b1a3d2-ff98-4a6f-9832-bbdb2300efdf")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Utilisateur supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Utilisateur non trouvé")
     *         )
     *     )
     * )
     */
    // 🔹 4. Supprimer un utilisateur
    #[Route('/delete/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(Users::class)->find($id);
        if (!$user) {
            return $this->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(['message' => 'Utilisateur supprimé avec succès']);
    }
}
