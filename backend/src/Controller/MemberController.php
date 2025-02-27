<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\Space;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Ramsey\Uuid\Uuid;

#[Route('/api/member')]
class MemberController extends AbstractController
{
    // 🔹 1. Créer un membre
    #[Route('/create', name: 'create_member', methods: ['POST'])]
    public function createMember(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['relationship'], $data['space_id'], $data['user_id'])) {
            return $this->json(['message' => 'Données invalides'], 400);
        }

        if (!Uuid::isValid($data['space_id']) || !Uuid::isValid($data['user_id'])) {
            return $this->json(['message' => 'ID de l\'espace ou de l\'utilisateur invalide'], 400);
        }

        $space = $entityManager->getRepository(Space::class)->find(Uuid::fromString($data['space_id']));
        $user = $entityManager->getRepository(Users::class)->find(Uuid::fromString($data['user_id']));

        if (!$space || !$user) {
            return $this->json(['message' => 'Espace ou utilisateur introuvable'], 404);
        }

        try {
            $member = new Member();
            $member->setName($data['name']);
            $member->setRelationship($data['relationship']);
            $member->setSpace($space);
            $member->setUser($user);

            $entityManager->persist($member);
            $entityManager->flush();

            return $this->json([
                'message' => 'Membre créé avec succès',
                'member_id' => $member->getId(),
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Erreur lors de la création du membre', 'error' => $e->getMessage()], 500);
        }
    }

    // 🔹 2. Récupérer tous les membres
    #[Route('/all', name: 'get_all_members', methods: ['GET'])]
    public function getAllMembers(EntityManagerInterface $entityManager): JsonResponse
    {
        $members = $entityManager->getRepository(Member::class)->findAll();
        $membersData = array_map(function (Member $member) {
            return [
                'id' => $member->getId(),
                'name' => $member->getName(),
                'relationship' => $member->getRelationship(),
                'space_id' => $member->getSpace()->getId(),
                'user_id' => $member->getUser()->getId(),
            ];
        }, $members);

        return $this->json($membersData);
    }

    // 🔹 3. Récupérer un membre par ID
    #[Route('/{id}', name: 'get_member', methods: ['GET'])]
    public function getMember(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['message' => 'ID invalide'], 400);
        }

        $member = $entityManager->getRepository(Member::class)->find(Uuid::fromString($id));
        if (!$member) {
            return $this->json(['message' => 'Membre non trouvé'], 404);
        }

        return $this->json([
            'id' => $member->getId(),
            'name' => $member->getName(),
            'relationship' => $member->getRelationship(),
            'space_id' => $member->getSpace()->getId(),
            'user_id' => $member->getUser()->getId(),
        ]);
    }

    // 🔹 4. Supprimer un membre
    #[Route('/delete/{id}', name: 'delete_member', methods: ['DELETE'])]
    public function deleteMember(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['message' => 'ID invalide'], 400);
        }

        $member = $entityManager->getRepository(Member::class)->find(Uuid::fromString($id));
        if (!$member) {
            return $this->json(['message' => 'Membre non trouvé'], 404);
        }

        $entityManager->remove($member);
        $entityManager->flush();

        return $this->json(['message' => 'Membre supprimé avec succès']);
    }
}
