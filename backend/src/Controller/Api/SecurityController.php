<?php
namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

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

    #[Route('/api/registro', name: 'api_registro', methods: ['POST'])]
    public function registro(Request $request, UserRepository $users, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): JsonResponse
    {
        try { $dados = $request->toArray(); } catch (\Throwable) { $dados = []; }
        $nome = trim((string) ($dados['nome'] ?? ''));
        $email = mb_strtolower(trim((string) ($dados['email'] ?? '')));
        $senha = (string) ($dados['senha'] ?? '');
        $violacoes = [];
        if ($nome === '') $violacoes['nome'] = 'O nome é obrigatório.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $violacoes['email'] = 'Informe um e-mail válido.';
        elseif ($users->findOneBy(['email' => $email])) $violacoes['email'] = 'Este e-mail já está cadastrado.';
        if (mb_strlen($senha) < 8) $violacoes['senha'] = 'A senha deve ter pelo menos 8 caracteres.';
        if ($violacoes) return $this->json(['erro' => 'Dados inválidos.', 'violacoes' => $violacoes], 422);
        $user = (new User())->setNome($nome)->setEmail($email)->setRoles([User::ROLE_USER]);
        $user->setPassword($hasher->hashPassword($user, $senha));
        $em->persist($user); $em->flush();
        return $this->userResponse($user, 201);
    }

    #[Route('/api/minha-conta', name: 'api_minha_conta_update', methods: ['PATCH'])]
    public function atualizarConta(Request $request, #[CurrentUser] User $user, UserRepository $users, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): JsonResponse
    {
        try { $dados = $request->toArray(); } catch (\Throwable) { $dados = []; }
        if (array_key_exists('nome', $dados)) { $nome = trim((string) $dados['nome']); if ($nome === '') return $this->json(['erro' => 'O nome é obrigatório.'], 422); $user->setNome($nome); }
        if (array_key_exists('email', $dados)) { $email = mb_strtolower(trim((string) $dados['email'])); $outro = $users->findOneBy(['email' => $email]); if (!filter_var($email, FILTER_VALIDATE_EMAIL) || ($outro && $outro !== $user)) return $this->json(['erro' => 'E-mail inválido ou já cadastrado.'], 422); $user->setEmail($email); }
        if (!empty($dados['senha'])) { if (mb_strlen((string) $dados['senha']) < 8) return $this->json(['erro' => 'A senha deve ter pelo menos 8 caracteres.'], 422); $user->setPassword($hasher->hashPassword($user, (string) $dados['senha'])); }
        $em->flush(); return $this->userResponse($user);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse { $request->getSession()->invalidate(); return new JsonResponse(null, 204); }
    private function userResponse(User $user, int $status = 200): JsonResponse { return $this->json(['id' => $user->getId(), 'nome' => $user->getNome(), 'email' => $user->getEmail(), 'roles' => $user->getRoles()], $status); }
}
