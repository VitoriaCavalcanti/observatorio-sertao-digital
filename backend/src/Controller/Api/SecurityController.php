<?php
namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class SecurityController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) return $this->json(['erro' => 'Credenciais inválidas.'], 401);
        return $this->userResponse($user);
    }
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(#[CurrentUser] User $user): JsonResponse { return $this->userResponse($user); }
    private function userResponse(User $user): JsonResponse { return $this->json(['id' => $user->getId(), 'nome' => $user->getNome(), 'email' => $user->getEmail(), 'roles' => $user->getRoles()]); }
}
