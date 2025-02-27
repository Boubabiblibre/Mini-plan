<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Ramsey\Uuid\Uuid;

#[Route('/api/service')]
class ServiceController extends AbstractController
{
    // 🔹 1. Créer un service
    #[Route('/create', name: 'create_service', methods: ['POST'])]
    public function createService(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['description'], $data['logo'], $data['category_id'])) {
            return $this->json(['message' => 'Données invalides'], 400);
        }

        // Vérification de l'UUID de la catégorie
        if (!Uuid::isValid($data['category_id'])) {
            return $this->json(['message' => 'ID de catégorie invalide'], 400);
        }

        // Recherche de la catégorie
        $category = $entityManager->getRepository(Category::class)->find(Uuid::fromString($data['category_id']));
        if (!$category) {
            return $this->json(['message' => 'Catégorie introuvable'], 404);
        }

        try {
            $service = new Service();
            $service->setName($data['name']);
            $service->setDescription($data['description']);
            $service->setProvider($data['provider'] ?? null);
            $service->setLogo($data['logo']);
            $service->setWebsite($data['website'] ?? null);
            $service->setCategory($category);

            $entityManager->persist($service);
            $entityManager->flush();

            return $this->json([
                'message' => 'Service créé avec succès',
                'service_id' => $service->getId(),
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Erreur lors de la création du service', 'error' => $e->getMessage()], 500);
        }
    }

    // 🔹 2. Récupérer tous les services
    #[Route('/all', name: 'get_all_services', methods: ['GET'])]
    public function getAllServices(EntityManagerInterface $entityManager): JsonResponse
    {
        $services = $entityManager->getRepository(Service::class)->findAll();
        $serviceData = array_map(function (Service $service) {
            return [
                'id' => $service->getId(),
                'name' => $service->getName(),
                'description' => $service->getDescription(),
                'provider' => $service->getProvider(),
                'logo' => $service->getLogo(),
                'website' => $service->getWebsite(),
                'category_id' => $service->getCategory()->getId(),
            ];
        }, $services);

        return $this->json($serviceData);
    }

    // 🔹 3. Récupérer un service par ID
    #[Route('/{id}', name: 'get_service', methods: ['GET'])]
    public function getService(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['message' => 'ID invalide'], 400);
        }

        $service = $entityManager->getRepository(Service::class)->find(Uuid::fromString($id));
        if (!$service) {
            return $this->json(['message' => 'Service non trouvé'], 404);
        }

        return $this->json([
            'id' => $service->getId(),
            'name' => $service->getName(),
            'description' => $service->getDescription(),
            'provider' => $service->getProvider(),
            'logo' => $service->getLogo(),
            'website' => $service->getWebsite(),
            'category_id' => $service->getCategory()->getId(),
        ]);
    }

    // 🔹 4. Mettre à jour un service
    #[Route('/update/{id}', name: 'update_service', methods: ['PUT'])]
    public function updateService(string $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['message' => 'ID invalide'], 400);
        }

        $service = $entityManager->getRepository(Service::class)->find(Uuid::fromString($id));
        if (!$service) {
            return $this->json(['message' => 'Service non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) $service->setName($data['name']);
        if (isset($data['description'])) $service->setDescription($data['description']);
        if (isset($data['provider'])) $service->setProvider($data['provider']);
        if (isset($data['logo'])) $service->setLogo($data['logo']);
        if (isset($data['website'])) $service->setWebsite($data['website']);

        if (isset($data['category_id'])) {
            if (!Uuid::isValid($data['category_id'])) {
                return $this->json(['message' => 'ID de catégorie invalide'], 400);
            }

            $category = $entityManager->getRepository(Category::class)->find(Uuid::fromString($data['category_id']));
            if (!$category) {
                return $this->json(['message' => 'Catégorie introuvable'], 404);
            }

            $service->setCategory($category);
        }

        $entityManager->flush();

        return $this->json(['message' => 'Service mis à jour avec succès']);
    }

    // 🔹 5. Supprimer un service
    #[Route('/delete/{id}', name: 'delete_service', methods: ['DELETE'])]
    public function deleteService(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['message' => 'ID invalide'], 400);
        }

        $service = $entityManager->getRepository(Service::class)->find(Uuid::fromString($id));
        if (!$service) {
            return $this->json(['message' => 'Service non trouvé'], 404);
        }

        $entityManager->remove($service);
        $entityManager->flush();

        return $this->json(['message' => 'Service supprimé avec succès']);
    }
}
