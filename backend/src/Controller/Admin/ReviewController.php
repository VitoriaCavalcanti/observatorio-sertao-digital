<?php

namespace App\Controller\Admin;

use App\Entity\CadastroHistorico;
use App\Entity\Indicador;
use App\Entity\Instituicao;
use App\Entity\Projeto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/revisoes')]
#[IsGranted('ROLE_EDITOR')]
final class ReviewController extends AbstractController
{
    private const MAP = ['instituicoes' => Instituicao::class, 'projetos' => Projeto::class, 'indicadores' => Indicador::class];

    #[Route('', name: 'admin_review_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $pendentes = [];
        foreach (self::MAP as $tipo => $class) foreach ($em->getRepository($class)->findAll() as $item) if ($this->isPending($item)) $pendentes[] = ['tipo' => $tipo, 'item' => $item, 'nome' => $this->name($item), 'alteracao' => $item->getDadosPendentes() !== null];
        return $this->render('admin/review/index.html.twig', ['pendentes' => $pendentes]);
    }

    #[Route('/{tipo}/{id}', name: 'admin_review_show', methods: ['GET'])]
    public function show(string $tipo, int $id, EntityManagerInterface $em): Response
    {
        $item = $this->find($tipo, $id, $em);
        $history = $em->getRepository(CadastroHistorico::class)->findBy(['tipo' => $tipo, 'registroId' => $id], ['criadoEm' => 'DESC']);
        return $this->render('admin/review/show.html.twig', ['tipo' => $tipo, 'item' => $item, 'nome' => $this->name($item), 'atual' => $this->snapshot($item), 'proposta' => $item->getDadosPendentes() ?? $this->snapshot($item), 'historico' => $history]);
    }

    #[Route('/{tipo}/{id}/{acao}', name: 'admin_review_action', methods: ['POST'], requirements: ['acao' => 'aprovar|devolver|rejeitar'])]
    public function action(string $tipo, int $id, string $acao, Request $request, #[CurrentUser] User $reviewer, EntityManagerInterface $em): Response
    {
        $item = $this->find($tipo, $id, $em);
        if (!$this->isCsrfTokenValid("review-$tipo-$id", (string) $request->request->get('_token'))) throw $this->createAccessDeniedException();
        $note = trim((string) $request->request->get('observacao')) ?: null;
        if ($acao === 'devolver' && !$note) { $this->addFlash('danger', 'Informe o motivo da devolução.'); return $this->redirectToRoute('admin_review_show', ['tipo' => $tipo, 'id' => $id]); }
        $data = $item->getDadosPendentes() ?? $this->snapshot($item);
        if ($acao === 'aprovar') {
            if ($item->getDadosPendentes()) $this->apply($item, $item->getDadosPendentes(), $em);
            $item->setStatusCadastro(Instituicao::CADASTRO_PUBLICADO)->setDadosPendentes(null)->setSituacaoRevisao(null)->setObservacaoRevisao(null);
        } elseif ($acao === 'devolver') {
            if ($item->getDadosPendentes()) $item->setSituacaoRevisao(Instituicao::CADASTRO_DEVOLVIDO); else $item->setStatusCadastro(Instituicao::CADASTRO_DEVOLVIDO);
            $item->setObservacaoRevisao($note);
        } else {
            if ($item->getStatusCadastro() === Instituicao::CADASTRO_PUBLICADO) $item->setDadosPendentes(null)->setSituacaoRevisao(null); else $item->setStatusCadastro(Instituicao::CADASTRO_REJEITADO);
            $item->setObservacaoRevisao($note);
        }
        $em->persist(new CadastroHistorico($tipo, $id, $acao, $item->getResponsavel(), $reviewer, $note, $data)); $em->flush();
        $this->addFlash('success', 'Revisão registrada.'); return $this->redirectToRoute('admin_review_index');
    }

    private function find(string $tipo, int $id, EntityManagerInterface $em): object { $class = self::MAP[$tipo] ?? throw $this->createNotFoundException(); return $em->getRepository($class)->find($id) ?? throw $this->createNotFoundException(); }
    private function isPending(object $item): bool { return $item->getStatusCadastro() === Instituicao::CADASTRO_EM_ANALISE || $item->getSituacaoRevisao() === Instituicao::CADASTRO_EM_ANALISE; }
    private function name(object $item): string { return $item instanceof Projeto ? (string) $item->getTitulo() : (string) $item->getNome(); }
    private function snapshot(object $item): array
    {
        if ($item instanceof Instituicao) return ['nome' => $item->getNome(), 'sigla' => $item->getSigla(), 'tipo' => $item->getTipo(), 'email' => $item->getEmail(), 'site' => $item->getSite(), 'municipio' => $item->getMunicipio(), 'uf' => $item->getUf()];
        if ($item instanceof Projeto) return ['titulo' => $item->getTitulo(), 'resumo' => $item->getResumo(), 'status' => $item->getStatus(), 'dataInicio' => $item->getDataInicio()?->format('Y-m-d'), 'dataFim' => $item->getDataFim()?->format('Y-m-d'), 'instituicaoId' => $item->getInstituicao()?->getId()];
        return ['nome' => $item->getNome(), 'descricao' => $item->getDescricao(), 'unidade' => $item->getUnidade(), 'valor' => $item->getValor(), 'anoReferencia' => $item->getAnoReferencia(), 'projetoId' => $item->getProjeto()?->getId()];
    }
    private function apply(object $item, array $data, EntityManagerInterface $em): void
    {
        if ($item instanceof Instituicao) foreach (['nome', 'sigla', 'tipo', 'email', 'site', 'municipio', 'uf'] as $field) if (array_key_exists($field, $data)) { $setter = 'set'.ucfirst($field); $item->$setter($data[$field]); }
        if ($item instanceof Projeto) {
            foreach (['titulo', 'resumo', 'status'] as $field) if (array_key_exists($field, $data)) { $setter = 'set'.ucfirst($field); $item->$setter($data[$field]); }
            foreach (['dataInicio', 'dataFim'] as $field) if (array_key_exists($field, $data)) { $setter = 'set'.ucfirst($field); $item->$setter($data[$field] ? new \DateTimeImmutable($data[$field]) : null); }
            if (array_key_exists('instituicaoId', $data)) $item->setInstituicao($data['instituicaoId'] ? $em->getRepository(Instituicao::class)->find($data['instituicaoId']) : null);
        }
        if ($item instanceof Indicador) {
            foreach (['nome', 'descricao', 'unidade'] as $field) if (array_key_exists($field, $data)) { $setter = 'set'.ucfirst($field); $item->$setter($data[$field]); }
            if (array_key_exists('valor', $data)) $item->setValor($data['valor'] !== null ? (float) $data['valor'] : null);
            if (array_key_exists('anoReferencia', $data)) $item->setAnoReferencia($data['anoReferencia'] !== null ? (int) $data['anoReferencia'] : null);
            if (array_key_exists('projetoId', $data)) $item->setProjeto($data['projetoId'] ? $em->getRepository(Projeto::class)->find($data['projetoId']) : null);
        }
    }
}
