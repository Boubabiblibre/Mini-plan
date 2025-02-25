<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/me', name: 'user_profile', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getProfile(Security $security): JsonResponse
    {
        $user = $security->getUser();

        if (!$user instanceof Users) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'phone_number' => $user->getPhoneNumber(),
            'age' => $user->getAge(),
            'avatar' => $user->getAvatar(),
            'is_active' => $user->isActive()
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/update', name: 'update_profile', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $user = $security->getUser();

        if (!$user instanceof Users) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['firstname'])) {
            $user->setFirstname($data['firstname']);
        }
        if (isset($data['lastname'])) {
            $user->setLastname($data['lastname']);
        }
        if (isset($data['phone_number'])) {
            $user->setPhoneNumber($data['phone_number']);
        }
        if (isset($data['age'])) {
            $user->setAge((int)$data['age']);
        }
        if (isset($data['avatar'])) {
            $user->setAvatar($data['avatar']);
        }

        $user->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Profil mis à jour avec succès'], JsonResponse::HTTP_OK);
    }
}
