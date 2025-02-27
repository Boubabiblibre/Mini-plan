<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/category')]
class CategoryController extends AbstractController
{
    // 🔹 1. Créer une nouvelle catégorie
    #[Route('/create', name: 'create_category', methods: ['POST'])]
    public function createCategory(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['type'], $data['color'])) {
            return $this->json(['message' => 'Données invalides'], 400);
        }

        try {
            $category = new Category();
            $category->setName($data['name']);
            $category->setDescription($data['description'] ?? null);
            $category->setType($data['type']);
            $category->setColor($data['color']);

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->json([
                'message' => 'Catégorie créée avec succès',
                'category_id' => $category->getId(),
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Erreur lors de la création de la catégorie', 'error' => $e->getMessage()], 500);
        }
    }

    // 🔹 2. Récupérer toutes les catégories
    #[Route('/all', name: 'get_all_categories', methods: ['GET'])]
    public function getAllCategories(EntityManagerInterface $entityManager): JsonResponse
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();
        $categoryData = array_map(function (Category $category) {
            return [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'type' => $category->getType(),
                'color' => $category->getColor(),
            ];
        }, $categories);

        return $this->json($categoryData);
    }

    // 🔹 3. Récupérer une catégorie par ID
    #[Route('/{id}', name: 'get_category', methods: ['GET'])]
    public function getCategory(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $category = $entityManager->getRepository(Category::class)->find($id);
        if (!$category) {
            return $this->json(['message' => 'Catégorie non trouvée'], 404);
        }

        return $this->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription(),
            'type' => $category->getType(),
            'color' => $category->getColor(),
        ]);
    }

    // 🔹 4. Mettre à jour une catégorie
    #[Route('/update/{id}', name: 'update_category', methods: ['PUT'])]
    public function updateCategory(string $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $category = $entityManager->getRepository(Category::class)->find($id);
        if (!$category) {
            return $this->json(['message' => 'Catégorie non trouvée'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $category->setName($data['name']);
        }
        if (isset($data['description'])) {
            $category->setDescription($data['description']);
        }
        if (isset($data['type'])) {
            $category->setType($data['type']);
        }
        if (isset($data['color'])) {
            $category->setColor($data['color']);
        }

        $entityManager->flush();

        return $this->json(['message' => 'Catégorie mise à jour avec succès']);
    }

    // 🔹 5. Supprimer une catégorie
    #[Route('/delete/{id}', name: 'delete_category', methods: ['DELETE'])]
    public function deleteCategory(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $category = $entityManager->getRepository(Category::class)->find($id);
        if (!$category) {
            return $this->json(['message' => 'Catégorie non trouvée'], 404);
        }

        $entityManager->remove($category);
        $entityManager->flush();

        return $this->json(['message' => 'Catégorie supprimée avec succès']);
    }
}
