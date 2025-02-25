<?php

namespace App\Controller;

use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\Users;

#[Route('/subscription')]
class SubscriptionController extends AbstractController
{
    #[Route('/', name: 'list_subscriptions', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $subscriptions = $entityManager->getRepository(Subscription::class)->findAll();

        $data = array_map(fn($subscription) => [
            'id' => $subscription->getId(),
            'name' => $subscription->getName(),
            'start_date' => $subscription->getStartDate()->format('Y-m-d'),
            'end_date' => $subscription->getEndDate() ? $subscription->getEndDate()->format('Y-m-d') : null,
            'amount' => $subscription->getAmount(),
            'status' => $subscription->getStatus(),
        ], $subscriptions);

        return $this->json($data);
    }

    #[Route('/my-subscriptions', name: 'user_subscriptions', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getUserSubscriptions(
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return new JsonResponse(['error' => 'Token JWT non trouvé'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $userData = $token->getUser();
        if (!$userData instanceof Users) {
            return new JsonResponse(['error' => 'Utilisateur invalide'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $userId = $userData->getId();
        $subscriptions = $entityManager->getRepository(Subscription::class)->findBy(['user' => $userId]);

        $data = array_map(fn($subscription) => [
            'id' => $subscription->getId(),
            'name' => $subscription->getName(),
            'start_date' => $subscription->getStartDate()->format('Y-m-d'),
            'end_date' => $subscription->getEndDate() ? $subscription->getEndDate()->format('Y-m-d') : null,
            'amount' => $subscription->getAmount(),
            'status' => $subscription->getStatus(),
        ], $subscriptions);

        return $this->json($data);
    }

    #[Route('/', name: 'create_subscription', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createSubscription(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['start_date'], $data['amount'], $data['status'])) {
            return $this->json(['error' => 'Données incomplètes'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $subscription = new Subscription();
        $subscription->setName($data['name']);
        $subscription->setStartDate(new \DateTime($data['start_date']));
        $subscription->setEndDate(isset($data['end_date']) ? new \DateTime($data['end_date']) : null);
        $subscription->setAmount((float) $data['amount']);
        $subscription->setStatus($data['status']);
        $subscription->setCreatedAt(new \DateTimeImmutable());
        $subscription->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($subscription);
        $entityManager->flush();

        return $this->json(['message' => 'Abonnement créé avec succès'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update_subscription', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function updateSubscription(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $subscription = $entityManager->getRepository(Subscription::class)->find($id);

        if (!$subscription) {
            return $this->json(['error' => 'Abonnement non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $subscription->setName($data['name']);
        }
        if (isset($data['start_date'])) {
            $subscription->setStartDate(new \DateTime($data['start_date']));
        }
        if (isset($data['end_date'])) {
            $subscription->setEndDate(new \DateTime($data['end_date']));
        }
        if (isset($data['amount'])) {
            $subscription->setAmount((float) $data['amount']);
        }
        if (isset($data['status'])) {
            $subscription->setStatus($data['status']);
        }

        $subscription->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($subscription);
        $entityManager->flush();

        return $this->json(['message' => 'Abonnement mis à jour avec succès'], JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete_subscription', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteSubscription(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $subscription = $entityManager->getRepository(Subscription::class)->find($id);

        if (!$subscription) {
            return $this->json(['error' => 'Abonnement non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($subscription);
        $entityManager->flush();

        return $this->json(['message' => 'Abonnement supprimé avec succès'], JsonResponse::HTTP_OK);
    }
}
