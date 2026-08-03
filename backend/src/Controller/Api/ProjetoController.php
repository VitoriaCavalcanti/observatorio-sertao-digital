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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/projetos')]
final class ProjetoController extends AbstractController
{
    #[Route('', name:'api_projeto_index', methods:['GET'])]
    public function index(ProjetoRepository $repo): JsonResponse { return $this->json(array_map($this->normalize(...), $repo->findBy(['statusCadastro' => Projeto::CADASTRO_PUBLICADO], ['titulo'=>'ASC']))); }
    #[Route('/{id}', name:'api_projeto_show', methods:['GET'])]
    public function show(Projeto $item): JsonResponse { if ($item->getStatusCadastro() !== Projeto::CADASTRO_PUBLICADO) throw $this->createNotFoundException(); return $this->json($this->normalize($item)); }
    #[Route('', name:'api_projeto_create', methods:['POST'])]
    public function create(Request $request, InstituicaoRepository $instituicoes, EntityManagerInterface $em): JsonResponse { $item=new Projeto(); $error=$this->fill($item,$this->payload($request),$instituicoes); if($error)return $error; $em->persist($item);$em->flush();return $this->json($this->normalize($item),201); }
    #[Route('/{id}', name:'api_projeto_update', methods:['PUT','PATCH'])]
    public function update(Projeto $item, Request $request, InstituicaoRepository $instituicoes, EntityManagerInterface $em): JsonResponse { $error=$this->fill($item,$this->payload($request),$instituicoes);if($error)return $error;$em->flush();return $this->json($this->normalize($item)); }
    #[Route('/{id}', name:'api_projeto_delete', methods:['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Projeto $item,EntityManagerInterface $em): JsonResponse {$em->remove($item);$em->flush();return new JsonResponse(null,204);}
    private function payload(Request $r):array{try{return $r->toArray();}catch(\Throwable){return[];}}
    private function fill(Projeto $p,array $d,InstituicaoRepository $repo):?JsonResponse{if(isset($d['titulo']))$p->setTitulo(trim((string)$d['titulo']));if(!$p->getTitulo())return $this->json(['erro'=>'Dados inválidos.','violacoes'=>['titulo'=>'O título é obrigatório.']],422);foreach(['resumo','status']as$f)if(array_key_exists($f,$d)){$s='set'.ucfirst($f);$p->$s($d[$f]!==''?$d[$f]:null);}foreach(['dataInicio','dataFim']as$f)if(array_key_exists($f,$d)){try{$s='set'.ucfirst($f);$p->$s($d[$f]?new \DateTimeImmutable($d[$f]):null);}catch(\Throwable){return $this->json(['erro'=>'Data inválida.'],422);}}if(array_key_exists('instituicaoId',$d)){$i=$d['instituicaoId']?$repo->find($d['instituicaoId']):null;if($d['instituicaoId']&&!$i)return $this->json(['erro'=>'Instituição não encontrada.'],404);$p->setInstituicao($i);}return null;}
    private function normalize(Projeto $p):array{return['id'=>$p->getId(),'titulo'=>$p->getTitulo(),'resumo'=>$p->getResumo(),'status'=>$p->getStatus(),'dataInicio'=>$p->getDataInicio()?->format('Y-m-d'),'dataFim'=>$p->getDataFim()?->format('Y-m-d'),'instituicao'=>$p->getInstituicao()?['id'=>$p->getInstituicao()->getId(),'nome'=>$p->getInstituicao()->getNome(),'sigla'=>$p->getInstituicao()->getSigla()]:null];}
}
