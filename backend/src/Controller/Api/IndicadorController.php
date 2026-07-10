<?php

namespace App\Controller\Api;

use App\Entity\Indicador;
use App\Repository\IndicadorRepository;
use App\Repository\ProjetoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/indicadores')]
final class IndicadorController extends AbstractController
{
    #[Route('', name: 'api_indicador_index', methods: ['GET'])]
    public function index(IndicadorRepository $indicadorRepository): JsonResponse
    {
        $indicadores = $indicadorRepository->findAll();

        $dados = array_map(function (Indicador $indicador) {
            return [
                'id' => $indicador->getId(),
                'nome' => $indicador->getNome(),
                'descricao' => $indicador->getDescricao(),
                'unidadeMedida' => $indicador->getUnidade(),
                'valor' => $indicador->getValor(),
                'dataReferencia' => $indicador->getDataReferencia()?->format('Y-m-d'),
                'projeto' => $indicador->getProjeto() ? [
                    'id' => $indicador->getProjeto()->getId(),
                    'titulo' => $indicador->getProjeto()->getTitulo(),
                ] : null,
            ];
        }, $indicadores);

        return $this->json($dados);
    }

    #[Route('', name: 'api_indicador_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        ProjetoRepository $projetoRepository
    ): JsonResponse {
        $dados = json_decode($request->getContent(), true);

        if (!is_array($dados) || empty($dados['nome'])) {
            return $this->json([
                'erro' => 'O campo nome é obrigatório.',
            ], 400);
        }

        $indicador = new Indicador();
        $indicador->setNome($dados['nome']);
        $indicador->setDescricao($dados['descricao'] ?? null);
        $indicador->setUnidade($dados['unidade'] ?? null);

        if (array_key_exists('valor', $dados) && $dados['valor'] !== null && $dados['valor'] !== '') {
            $indicador->setValor((float) $dados['valor']);
        }

       if (!empty($dados['anoReferencia'])) {
    $indicador->setAnoReferencia((int) $dados['anoReferencia']);
        }

        if (!empty($dados['projetoId'])) {
            $projeto = $projetoRepository->find($dados['projetoId']);

            if (!$projeto) {
                return $this->json([
                    'erro' => 'Projeto não encontrado.',
                ], 404);
            }

            $indicador->setProjeto($projeto);
        }

        $entityManager->persist($indicador);
        $entityManager->flush();

        return $this->json([
            'id' => $indicador->getId(),
            'nome' => $indicador->getNome(),
            'descricao' => $indicador->getDescricao(),
            'unidade' => $indicador->getUnidade(),
            'valor' => $indicador->getValor(),
            'anoReferencia' => $indicador->getAnoReferencia(),
            'projetoId' => $indicador->getProjeto()?->getId(),
        ], 201);
    }
}