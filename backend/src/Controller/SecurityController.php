<?php

namespace App\Controller;  
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository; 
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, UserRepository $userRepository
    ): JsonResponse {
        $firstname = $request->request->get('firstname');
        $password = $request->request->get('password');

        // Rechercher l'utilisateur
        $user = $userRepository->findOneBy(['firstname' => $firstname]);

        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Utilisateur non trouvé']);
        }
        // Soit on return un JWT encrytper un token
        // Soit pour l'instant on renvoie juste true
        
        return $this->json(["success" => True]);
        
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
