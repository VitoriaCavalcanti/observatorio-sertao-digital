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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/indicadores')]
final class IndicadorController extends AbstractController
{
    #[Route('',name:'api_indicador_index',methods:['GET'])]public function index(IndicadorRepository $r):JsonResponse{return $this->json(array_map($this->normalize(...),$r->findBy(['statusCadastro'=>Indicador::CADASTRO_PUBLICADO],['nome'=>'ASC'])));}
    #[Route('/{id}',name:'api_indicador_show',methods:['GET'])]public function show(Indicador $i):JsonResponse{if($i->getStatusCadastro()!==Indicador::CADASTRO_PUBLICADO)throw$this->createNotFoundException();return$this->json($this->normalize($i));}
    #[Route('',name:'api_indicador_create',methods:['POST'])]public function create(Request $r,ProjetoRepository $pr,EntityManagerInterface $em):JsonResponse{$i=new Indicador();$e=$this->fill($i,$this->payload($r),$pr);if($e)return$e;$em->persist($i);$em->flush();return$this->json($this->normalize($i),201);}
    #[Route('/{id}',name:'api_indicador_update',methods:['PUT','PATCH'])]public function update(Indicador $i,Request $r,ProjetoRepository $pr,EntityManagerInterface $em):JsonResponse{$e=$this->fill($i,$this->payload($r),$pr);if($e)return$e;$em->flush();return$this->json($this->normalize($i));}
    #[Route('/{id}',name:'api_indicador_delete',methods:['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Indicador $i,EntityManagerInterface $em):JsonResponse{$em->remove($i);$em->flush();return new JsonResponse(null,204);}
    private function payload(Request $r):array{try{return$r->toArray();}catch(\Throwable){return[];}}
    private function fill(Indicador $i,array $d,ProjetoRepository $repo):?JsonResponse{if(isset($d['nome']))$i->setNome(trim((string)$d['nome']));if(!$i->getNome())return$this->json(['erro'=>'Dados inválidos.','violacoes'=>['nome'=>'O nome é obrigatório.']],422);foreach(['descricao','unidade']as$f)if(array_key_exists($f,$d)){$s='set'.ucfirst($f);$i->$s($d[$f]!==''?$d[$f]:null);}if(array_key_exists('valor',$d))$i->setValor($d['valor']!==null&&$d['valor']!==''?(float)$d['valor']:null);if(array_key_exists('anoReferencia',$d))$i->setAnoReferencia($d['anoReferencia']?(int)$d['anoReferencia']:null);if(array_key_exists('projetoId',$d)){$p=$d['projetoId']?$repo->find($d['projetoId']):null;if($d['projetoId']&&!$p)return$this->json(['erro'=>'Projeto não encontrado.'],404);$i->setProjeto($p);}return null;}
    private function normalize(Indicador $i):array{return['id'=>$i->getId(),'nome'=>$i->getNome(),'descricao'=>$i->getDescricao(),'unidade'=>$i->getUnidade(),'valor'=>$i->getValor(),'anoReferencia'=>$i->getAnoReferencia(),'projeto'=>$i->getProjeto()?['id'=>$i->getProjeto()->getId(),'titulo'=>$i->getProjeto()->getTitulo()]:null];}
}
