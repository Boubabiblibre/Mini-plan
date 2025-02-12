<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/user')]
final class UserController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/api/test-auth', name: 'test_auth', methods: ['GET'])]
    public function testAuth(): JsonResponse
    {
        $user = $this->getUser();
        return $this->json([
            'user' => $user ? $user->getUserIdentifier() : null,
        ]);
    }
    
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        $user = new User();
        $user->setFirstname($payload['firstname']);
        $user->setLastname($payload['lastname']);
        $user->setEmail($payload['email']);
        $user->setPhoneNumber($payload['phone_number']);
        $user->setAge($payload['age']);
        $user->setAvatar($payload['avatar']);
        $user->setPassword($payload['password']);

        $entityManager->persist($user);

        $entityManager->flush();

        return new JsonResponse($user, 200);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'app_user_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);
    
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Vérification que l'utilisateur authentifié peut modifier ce compte
        $currentUser = $this->security->getUser();
        if ($currentUser !== $user) {
            return $this->json(['error' => 'Access Denied'], Response::HTTP_FORBIDDEN);
        }
    
        $payload = json_decode($request->getContent(), true);
    
        if (isset($payload['firstname'])) {
            $user->setFirstname($payload['firstname']);
        }
        if (isset($payload['lastname'])) {
            $user->setLastname($payload['lastname']);
        }
    
        $entityManager->flush();
    
        return $this->json([
            'message' => 'User updated successfully',
            'user' => [
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
            ]
        ]);
    }    

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    // #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
