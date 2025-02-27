<?php

namespace App\Controller;

use App\Entity\Space;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Ramsey\Uuid\Uuid;

#[Route('/api/space')]
class SpaceController extends AbstractController
{
    // 🔹 1. Créer un espace
    #[Route('/create', name: 'create_space', methods: ['POST'])]
    public function createSpace(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['logo'], $data['description'])) {
            return $this->json(['message' => 'Données invalides'], 400);
        }

        try {
            $space = new Space();
            $space->setName($data['name']);
            $space->setLogo($data['logo']);
            $space->setDescription($data['description']);

            $entityManager->persist($space);
            $entityManager->flush();

            return $this->json([
                'message' => 'Espace créé avec succès',
                'space_id' => $space->getId(),
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Erreur lors de la création de l’espace', 'error' => $e->getMessage()], 500);
        }
    }

    // 🔹 2. Récupérer tous les espaces
    #[Route('/all', name: 'get_all_spaces', methods: ['GET'])]
    public function getAllSpaces(EntityManagerInterface $entityManager): JsonResponse
    {
        $spaces = $entityManager->getRepository(Space::class)->findAll();
        $spaceData = array_map(function (Space $space) {
            return [
                'id' => $space->getId(),
                'name' => $space->getName(),
                'logo' => $space->getLogo(),
                'description' => $space->getDescription(),
                'created_at' => $space->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $spaces);

        return $this->json($spaceData);
    }

    // 🔹 3. Récupérer un espace par ID
    #[Route('/{id}', name: 'get_space', methods: ['GET'])]
    public function getSpace(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['message' => 'ID invalide'], 400);
        }

        $space = $entityManager->getRepository(Space::class)->find(Uuid::fromString($id));
        if (!$space) {
            return $this->json(['message' => 'Espace non trouvé'], 404);
        }

        return $this->json([
            'id' => $space->getId(),
            'name' => $space->getName(),
            'logo' => $space->getLogo(),
            'description' => $space->getDescription(),
            'created_at' => $space->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    // 🔹 4. Mettre à jour un espace
    #[Route('/update/{id}', name: 'update_space', methods: ['PUT'])]
    public function updateSpace(string $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['message' => 'ID invalide'], 400);
        }

        $space = $entityManager->getRepository(Space::class)->find(Uuid::fromString($id));
        if (!$space) {
            return $this->json(['message' => 'Espace non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) {
            $space->setName($data['name']);
        }
        if (isset($data['logo'])) {
            $space->setLogo($data['logo']);
        }
        if (isset($data['description'])) {
            $space->setDescription($data['description']);
        }

        $entityManager->flush();

        return $this->json(['message' => 'Espace mis à jour avec succès']);
    }

    // 🔹 5. Supprimer un espace
    #[Route('/delete/{id}', name: 'delete_space', methods: ['DELETE'])]
    public function deleteSpace(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['message' => 'ID invalide'], 400);
        }

        $space = $entityManager->getRepository(Space::class)->find(Uuid::fromString($id));
        if (!$space) {
            return $this->json(['message' => 'Espace non trouvé'], 404);
        }

        $entityManager->remove($space);
        $entityManager->flush();

        return $this->json(['message' => 'Espace supprimé avec succès']);
    }
}
