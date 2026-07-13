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
    public function index(InstituicaoRepository $repo): JsonResponse { return $this->json(array_map($this->normalize(...), $repo->findBy([], ['nome' => 'ASC']))); }
    #[Route('/{id}', name: 'api_instituicao_show', methods: ['GET'])]
    public function show(Instituicao $item): JsonResponse { return $this->json($this->normalize($item)); }
    #[Route('', name: 'api_instituicao_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse { $item = new Instituicao(); $error = $this->fill($item, $this->payload($request)); if ($error) return $error; $em->persist($item); $em->flush(); return $this->json($this->normalize($item), 201); }
    #[Route('/{id}', name: 'api_instituicao_update', methods: ['PUT', 'PATCH'])]
    public function update(Instituicao $item, Request $request, EntityManagerInterface $em): JsonResponse { $error = $this->fill($item, $this->payload($request)); if ($error) return $error; $em->flush(); return $this->json($this->normalize($item)); }
    #[Route('/{id}', name: 'api_instituicao_delete', methods: ['DELETE'])]
    public function delete(Instituicao $item, EntityManagerInterface $em): JsonResponse { $em->remove($item); $em->flush(); return new JsonResponse(null, 204); }
    private function payload(Request $request): array { try { return $request->toArray(); } catch (\Throwable) { return []; } }
    private function fill(Instituicao $i, array $d): ?JsonResponse { if (isset($d['nome'])) $i->setNome(trim((string) $d['nome'])); if (!$i->getNome()) return $this->json(['erro' => 'Dados inválidos.', 'violacoes' => ['nome' => 'O nome é obrigatório.']], 422); foreach (['sigla','tipo','email','site','municipio','uf'] as $field) { if (array_key_exists($field, $d)) { $setter = 'set'.ucfirst($field); $i->$setter($d[$field] !== '' ? $d[$field] : null); } } return null; }
    private function normalize(Instituicao $i): array { return ['id'=>$i->getId(),'nome'=>$i->getNome(),'sigla'=>$i->getSigla(),'tipo'=>$i->getTipo(),'email'=>$i->getEmail(),'site'=>$i->getSite(),'municipio'=>$i->getMunicipio(),'uf'=>$i->getUf()]; }
}
