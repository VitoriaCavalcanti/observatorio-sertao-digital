<?php

namespace App\Controller\Api;

use App\Entity\Instituicao;
use App\Repository\InstituicaoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/instituicoes')]
final class InstituicaoController extends AbstractController
{
    #[Route('', name: 'api_instituicao_index', methods: ['GET'])]
    public function index(InstituicaoRepository $instituicaoRepository): JsonResponse
    {
        $instituicoes = $instituicaoRepository->findAll();

        $dados = array_map(function (Instituicao $instituicao) {
            return [
                'id' => $instituicao->getId(),
                'nome' => $instituicao->getNome(),
                'sigla' => $instituicao->getSigla(),
                'tipo' => $instituicao->getTipo(),
                'email' => $instituicao->getEmail(),
                'site' => $instituicao->getSite(),
                'municipio' => $instituicao->getMunicipio(),
                'uf' => $instituicao->getUf(),
            ];
        }, $instituicoes);

        return $this->json($dados);
    }

    #[Route('', name: 'api_instituicao_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $dados = json_decode($request->getContent(), true);

        if (!is_array($dados) || empty($dados['nome'])) {
            return $this->json([
                'erro' => 'O campo nome é obrigatório.',
            ], 400);
        }

        $instituicao = new Instituicao();
        $instituicao->setNome($dados['nome']);
        $instituicao->setSigla($dados['sigla'] ?? null);
        $instituicao->setTipo($dados['tipo'] ?? null);
        $instituicao->setEmail($dados['email'] ?? null);
        $instituicao->setSite($dados['site'] ?? null);
        $instituicao->setMunicipio($dados['municipio'] ?? null);
        $instituicao->setUf($dados['uf'] ?? null);

        $entityManager->persist($instituicao);
        $entityManager->flush();

        return $this->json([
            'id' => $instituicao->getId(),
            'nome' => $instituicao->getNome(),
            'sigla' => $instituicao->getSigla(),
            'tipo' => $instituicao->getTipo(),
            'email' => $instituicao->getEmail(),
            'site' => $instituicao->getSite(),
            'municipio' => $instituicao->getMunicipio(),
            'uf' => $instituicao->getUf(),
        ], 201);
    }
}