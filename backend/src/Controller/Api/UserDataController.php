<?php

namespace App\Controller\Api;

use App\Entity\Indicador;
use App\Entity\Instituicao;
use App\Entity\Projeto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/meus-cadastros')]
final class UserDataController extends AbstractController
{
    private const MAP = ['instituicoes' => Instituicao::class, 'projetos' => Projeto::class, 'indicadores' => Indicador::class];

    #[Route('', name: 'api_meus_cadastros', methods: ['GET'])]
    public function index(#[CurrentUser] User $user, EntityManagerInterface $em): JsonResponse
    {
        $resultado = [];
        foreach (self::MAP as $tipo => $class) {
            $resultado[$tipo] = array_map($this->normalize(...), $em->getRepository($class)->findBy(['responsavel' => $user], ['id' => 'DESC']));
        }
        return $this->json($resultado);
    }

    #[Route('/{tipo}', name: 'api_meu_cadastro_create', methods: ['POST'])]
    public function create(string $tipo, Request $request, #[CurrentUser] User $user, EntityManagerInterface $em): JsonResponse
    {
        $class = $this->classFor($tipo);
        $item = (new $class())->setResponsavel($user);
        if ($error = $this->fill($item, $this->payload($request), $user, $em)) return $error;
        $em->persist($item); $em->flush();
        return $this->json($this->normalize($item), 201);
    }

    #[Route('/{tipo}/{id}', name: 'api_meu_cadastro_update', methods: ['PATCH'])]
    public function update(string $tipo, int $id, Request $request, #[CurrentUser] User $user, EntityManagerInterface $em): JsonResponse
    {
        $item = $this->owned($tipo, $id, $user, $em);
        if ($item->getStatusCadastro() === Instituicao::CADASTRO_EM_ANALISE) return $this->json(['erro' => 'O cadastro está em análise e não pode ser alterado.'], 409);
        if ($error = $this->fill($item, $this->payload($request), $user, $em)) return $error;
        $item->setStatusCadastro(Instituicao::CADASTRO_RASCUNHO);
        $em->flush(); return $this->json($this->normalize($item));
    }

    #[Route('/{tipo}/{id}/enviar', name: 'api_meu_cadastro_submit', methods: ['POST'])]
    public function submit(string $tipo, int $id, #[CurrentUser] User $user, EntityManagerInterface $em): JsonResponse
    {
        $item = $this->owned($tipo, $id, $user, $em);
        $item->setStatusCadastro(Instituicao::CADASTRO_EM_ANALISE);
        $em->flush(); return $this->json($this->normalize($item));
    }

    private function owned(string $tipo, int $id, User $user, EntityManagerInterface $em): object
    {
        $item = $em->getRepository($this->classFor($tipo))->find($id);
        if (!$item || $item->getResponsavel() !== $user) throw $this->createNotFoundException();
        return $item;
    }

    private function classFor(string $tipo): string { return self::MAP[$tipo] ?? throw $this->createNotFoundException('Tipo inválido.'); }
    private function payload(Request $request): array { try { return $request->toArray(); } catch (\Throwable) { return []; } }

    private function fill(object $item, array $data, User $user, EntityManagerInterface $em): ?JsonResponse
    {
        if ($item instanceof Instituicao) {
            if (array_key_exists('nome', $data)) $item->setNome(trim((string) $data['nome']));
            if (!$item->getNome()) return $this->json(['erro' => 'O nome é obrigatório.'], 422);
            foreach (['sigla', 'tipo', 'email', 'site', 'municipio', 'uf'] as $field) if (array_key_exists($field, $data)) { $setter = 'set'.ucfirst($field); $item->$setter($data[$field] !== '' ? $data[$field] : null); }
        }
        if ($item instanceof Projeto) {
            if (array_key_exists('titulo', $data)) $item->setTitulo(trim((string) $data['titulo']));
            if (!$item->getTitulo()) return $this->json(['erro' => 'O título é obrigatório.'], 422);
            foreach (['resumo', 'status'] as $field) if (array_key_exists($field, $data)) { $setter = 'set'.ucfirst($field); $item->$setter($data[$field] !== '' ? $data[$field] : null); }
            foreach (['dataInicio', 'dataFim'] as $field) if (array_key_exists($field, $data)) { try { $setter = 'set'.ucfirst($field); $item->$setter($data[$field] ? new \DateTimeImmutable($data[$field]) : null); } catch (\Throwable) { return $this->json(['erro' => 'Data inválida.'], 422); } }
            if (array_key_exists('instituicaoId', $data)) { $instituicao = $data['instituicaoId'] ? $em->getRepository(Instituicao::class)->find($data['instituicaoId']) : null; if ($instituicao && $instituicao->getResponsavel() !== $user) return $this->json(['erro' => 'Instituição inválida.'], 422); $item->setInstituicao($instituicao); }
        }
        if ($item instanceof Indicador) {
            if (array_key_exists('nome', $data)) $item->setNome(trim((string) $data['nome']));
            if (!$item->getNome()) return $this->json(['erro' => 'O nome é obrigatório.'], 422);
            foreach (['descricao', 'unidade'] as $field) if (array_key_exists($field, $data)) { $setter = 'set'.ucfirst($field); $item->$setter($data[$field] !== '' ? $data[$field] : null); }
            if (array_key_exists('valor', $data)) $item->setValor($data['valor'] !== '' && $data['valor'] !== null ? (float) $data['valor'] : null);
            if (array_key_exists('anoReferencia', $data)) $item->setAnoReferencia($data['anoReferencia'] ? (int) $data['anoReferencia'] : null);
            if (array_key_exists('projetoId', $data)) { $projeto = $data['projetoId'] ? $em->getRepository(Projeto::class)->find($data['projetoId']) : null; if ($projeto && $projeto->getResponsavel() !== $user) return $this->json(['erro' => 'Projeto inválido.'], 422); $item->setProjeto($projeto); }
        }
        return null;
    }

    private function normalize(object $item): array
    {
        $base = ['id' => $item->getId(), 'statusCadastro' => $item->getStatusCadastro()];
        if ($item instanceof Instituicao) return $base + ['nome' => $item->getNome(), 'sigla' => $item->getSigla(), 'tipo' => $item->getTipo(), 'email' => $item->getEmail(), 'site' => $item->getSite(), 'municipio' => $item->getMunicipio(), 'uf' => $item->getUf()];
        if ($item instanceof Projeto) return $base + ['titulo' => $item->getTitulo(), 'resumo' => $item->getResumo(), 'status' => $item->getStatus(), 'dataInicio' => $item->getDataInicio()?->format('Y-m-d'), 'dataFim' => $item->getDataFim()?->format('Y-m-d'), 'instituicaoId' => $item->getInstituicao()?->getId()];
        return $base + ['nome' => $item->getNome(), 'descricao' => $item->getDescricao(), 'unidade' => $item->getUnidade(), 'valor' => $item->getValor(), 'anoReferencia' => $item->getAnoReferencia(), 'projetoId' => $item->getProjeto()?->getId()];
    }
}
