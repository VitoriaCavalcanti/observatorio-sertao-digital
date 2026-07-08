<?php

namespace App\Controller\Api;

use App\Entity\Projeto;
use App\Repository\InstituicaoRepository;
use App\Repository\ProjetoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/projetos')]
final class ProjetoController extends AbstractController
{
    #[Route('', name: 'api_projeto_index', methods: ['GET'])]
    public function index(ProjetoRepository $projetoRepository): JsonResponse
    {
        $projetos = $projetoRepository->findAll();

        $dados = array_map(function (Projeto $projeto) {
            return [
                'id' => $projeto->getId(),
                'titulo' => $projeto->getTitulo(),
                'resumo' => $projeto->getResumo(),
                'status' => $projeto->getStatus(),
                'dataInicio' => $projeto->getDataInicio()?->format('Y-m-d'),
                'dataFim' => $projeto->getDataFim()?->format('Y-m-d'),
                'instituicao' => $projeto->getInstituicao() ? [
                    'id' => $projeto->getInstituicao()->getId(),
                    'nome' => $projeto->getInstituicao()->getNome(),
                    'sigla' => $projeto->getInstituicao()->getSigla(),
                ] : null,
            ];
        }, $projetos);

        return $this->json($dados);
    }

    #[Route('', name: 'api_projeto_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        InstituicaoRepository $instituicaoRepository
    ): JsonResponse {
        $dados = json_decode($request->getContent(), true);

        if (!is_array($dados) || empty($dados['titulo'])) {
            return $this->json([
                'erro' => 'O campo titulo é obrigatório.',
            ], 400);
        }

        $projeto = new Projeto();
        $projeto->setTitulo($dados['titulo']);
        $projeto->setResumo($dados['resumo'] ?? null);
        $projeto->setStatus($dados['status'] ?? null);

        if (!empty($dados['dataInicio'])) {
            $projeto->setDataInicio(new \DateTimeImmutable($dados['dataInicio']));
        }

        if (!empty($dados['dataFim'])) {
            $projeto->setDataFim(new \DateTimeImmutable($dados['dataFim']));
        }

        if (!empty($dados['instituicaoId'])) {
            $instituicao = $instituicaoRepository->find($dados['instituicaoId']);

            if (!$instituicao) {
                return $this->json([
                    'erro' => 'Instituicao não encontrada.',
                ], 404);
            }

            $projeto->setInstituicao($instituicao);
        }

        $entityManager->persist($projeto);
        $entityManager->flush();

        return $this->json([
            'id' => $projeto->getId(),
            'titulo' => $projeto->getTitulo(),
            'resumo' => $projeto->getResumo(),
            'status' => $projeto->getStatus(),
            'dataInicio' => $projeto->getDataInicio()?->format('Y-m-d'),
            'dataFim' => $projeto->getDataFim()?->format('Y-m-d'),
            'instituicaoId' => $projeto->getInstituicao()?->getId(),
        ], 201);
    }
}