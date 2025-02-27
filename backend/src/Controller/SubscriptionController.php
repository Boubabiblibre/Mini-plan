<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Entity\Member;
use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/subscription')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class SubscriptionController extends AbstractController
{
    // 🔹 1. Créer un abonnement
    #[Route('/create', name: 'create_subscription', methods: ['POST'])]
    public function createSubscription(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérification des données obligatoires
        if (!isset($data['name'], $data['subscription_type'], $data['start_date'], $data['amount'], $data['billing_mode'], $data['status'], $data['member_id'], $data['service_id'])) {
            return $this->json(['message' => 'Données invalides'], 400);
        }

        // Vérifier si les IDs fournis sont bien des UUIDs valides
        if (!Uuid::isValid($data['member_id']) || !Uuid::isValid($data['service_id'])) {
            return $this->json(['message' => 'ID de Member ou Service invalide'], 400);
        }

        // Convertir les IDs en UUIDs pour Doctrine
        $memberId = Uuid::fromString($data['member_id']);
        $serviceId = Uuid::fromString($data['service_id']);

        // Rechercher le membre et le service
        $member = $entityManager->getRepository(Member::class)->find($memberId);
        $service = $entityManager->getRepository(Service::class)->find($serviceId);


        // dump($data['member_id'], $data['service_id']); // 🔍 Vérifie les valeurs envoyées
        // dump($member, $service); // 🔍 Vérifie si Doctrine trouve les entités
        // die(); // Arrête l'exécution pour voir le debug

        if (!$member || !$service) {
            return $this->json(['message' => 'Membre ou Service introuvable'], 404);
        }

        try {
            // Création de l'abonnement
            $subscription = new Subscription();
            $subscription->setName($data['name']);
            $subscription->setNotes($data['notes'] ?? null);
            $subscription->setSubscriptionType($data['subscription_type']);
            $subscription->setStartDate(new \DateTime($data['start_date']));
            $subscription->setEndDate(isset($data['end_date']) ? new \DateTime($data['end_date']) : null);
            $subscription->setAmount((float)$data['amount']);
            $subscription->setTotalPaid((float)($data['total_paid'] ?? 0));
            $subscription->setAutoRenewal($data['auto_renewal'] ?? false);
            $subscription->setBillingMode($data['billing_mode']);
            $subscription->setBillingFrequency($data['billing_frequency'] ?? null);
            $subscription->setBillingDay(isset($data['billing_day']) ? new \DateTime($data['billing_day']) : null);
            $subscription->setStatus($data['status']);
            $subscription->setMember($member);
            $subscription->setService($service);

            // Sauvegarde dans la base de données
            $entityManager->persist($subscription);
            $entityManager->flush();

            return $this->json([
                'message' => 'Abonnement créé avec succès',
                'subscription_id' => $subscription->getId(),
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Erreur lors de la création de l’abonnement', 'error' => $e->getMessage()], 500);
        }
    }

    // 🔹 2. Récupérer tous les abonnements
    #[Route('/all', name: 'get_all_subscriptions', methods: ['GET'])]
    public function getAllSubscriptions(EntityManagerInterface $entityManager): JsonResponse
    {
        $subscriptions = $entityManager->getRepository(Subscription::class)->findAll();
        $subscriptionData = array_map(function (Subscription $subscription) {
            return [
                'id' => $subscription->getId(),
                'name' => $subscription->getName(),
                'notes' => $subscription->getNotes(),
                'subscription_type' => $subscription->getSubscriptionType(),
                'start_date' => $subscription->getStartDate()->format('Y-m-d'),
                'end_date' => $subscription->getEndDate()?->format('Y-m-d'),
                'amount' => $subscription->getAmount(),
                'billing_mode' => $subscription->getBillingMode(),
                'status' => $subscription->getStatus(),
                'member_id' => $subscription->getMember()->getId(),
                'service_id' => $subscription->getService()->getId(),
            ];
        }, $subscriptions);

        return $this->json($subscriptionData);
    }

    // 🔹 3. Récupérer un abonnement par ID
    #[Route('/{id}', name: 'get_subscription', methods: ['GET'])]
    public function getSubscription(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['message' => 'ID invalide'], 400);
        }

        $subscription = $entityManager->getRepository(Subscription::class)->find(Uuid::fromString($id));
        if (!$subscription) {
            return $this->json(['message' => 'Abonnement non trouvé'], 404);
        }

        return $this->json([
            'id' => $subscription->getId(),
            'name' => $subscription->getName(),
            'notes' => $subscription->getNotes(),
            'subscription_type' => $subscription->getSubscriptionType(),
            'start_date' => $subscription->getStartDate()->format('Y-m-d'),
            'end_date' => $subscription->getEndDate()?->format('Y-m-d'),
            'amount' => $subscription->getAmount(),
            'billing_mode' => $subscription->getBillingMode(),
            'status' => $subscription->getStatus(),
            'member_id' => $subscription->getMember()->getId(),
            'service_id' => $subscription->getService()->getId(),
        ]);
    }

    // 🔹 4. Supprimer un abonnement
    #[Route('/delete/{id}', name: 'delete_subscription', methods: ['DELETE'])]
    public function deleteSubscription(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['message' => 'ID invalide'], 400);
        }

        $subscription = $entityManager->getRepository(Subscription::class)->find(Uuid::fromString($id));
        if (!$subscription) {
            return $this->json(['message' => 'Abonnement non trouvé'], 404);
        }

        $entityManager->remove($subscription);
        $entityManager->flush();

        return $this->json(['message' => 'Abonnement supprimé avec succès']);
    }
}
