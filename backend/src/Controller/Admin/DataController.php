<?php
namespace App\Controller\Admin;

use App\Entity\Indicador;
use App\Entity\Instituicao;
use App\Entity\Projeto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/dados')]
#[IsGranted('ROLE_EDITOR')]
final class DataController extends AbstractController
{
    private const MAP = ['instituicoes' => Instituicao::class, 'projetos' => Projeto::class, 'indicadores' => Indicador::class];

    #[Route('/{tipo}', name: 'admin_data_index', methods: ['GET'])]
    public function index(string $tipo, EntityManagerInterface $em): Response
    {
        $class = $this->classFor($tipo);
        return $this->render('admin/data/index.html.twig', ['tipo' => $tipo, 'itens' => $em->getRepository($class)->findBy([], ['id' => 'DESC'])]);
    }

    #[Route('/{tipo}/novo', name: 'admin_data_new', methods: ['GET', 'POST'])]
    public function new(string $tipo, Request $request, EntityManagerInterface $em): Response
    {
        $class = $this->classFor($tipo);
        return $this->form($tipo, $request, new $class(), $em);
    }

    #[Route('/{tipo}/{id}/editar', name: 'admin_data_edit', methods: ['GET', 'POST'])]
    public function edit(string $tipo, int $id, Request $request, EntityManagerInterface $em): Response
    {
        $item = $em->getRepository($this->classFor($tipo))->find($id);
        if (!$item) throw $this->createNotFoundException();
        return $this->form($tipo, $request, $item, $em);
    }

    #[Route('/{tipo}/{id}/excluir', name: 'admin_data_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(string $tipo, int $id, Request $request, EntityManagerInterface $em): Response
    {
        $item = $em->getRepository($this->classFor($tipo))->find($id);
        if ($item && $this->isCsrfTokenValid('delete-'.$tipo.'-'.$id, (string) $request->request->get('_token'))) { $em->remove($item); $em->flush(); $this->addFlash('success', 'Registro excluído.'); }
        return $this->redirectToRoute('admin_data_index', ['tipo' => $tipo]);
    }

    private function form(string $tipo, Request $request, object $item, EntityManagerInterface $em): Response
    {
        $builder = $this->createFormBuilder($item);
        if ($item instanceof Instituicao) $builder->add('nome')->add('sigla')->add('tipo')->add('email')->add('site')->add('municipio')->add('uf');
        if ($item instanceof Projeto) $builder->add('titulo')->add('resumo', TextareaType::class, ['required' => false])->add('status')->add('dataInicio', DateType::class, ['required' => false, 'widget' => 'single_text'])->add('dataFim', DateType::class, ['required' => false, 'widget' => 'single_text'])->add('instituicao', EntityType::class, ['class' => Instituicao::class, 'choice_label' => 'nome', 'required' => false]);
        if ($item instanceof Indicador) $builder->add('nome')->add('descricao', TextareaType::class, ['required' => false])->add('unidade')->add('valor', NumberType::class, ['required' => false])->add('anoReferencia')->add('projeto', EntityType::class, ['class' => Projeto::class, 'choice_label' => 'titulo', 'required' => false]);
        $builder->add('responsavel', EntityType::class, ['class' => User::class, 'choice_label' => 'email', 'required' => false, 'label' => 'Usuário responsável'])
            ->add('statusCadastro', ChoiceType::class, ['label' => 'Situação do cadastro', 'choices' => ['Rascunho' => Instituicao::CADASTRO_RASCUNHO, 'Em análise' => Instituicao::CADASTRO_EM_ANALISE, 'Publicado' => Instituicao::CADASTRO_PUBLICADO, 'Devolvido para correção' => Instituicao::CADASTRO_DEVOLVIDO, 'Rejeitado' => Instituicao::CADASTRO_REJEITADO]]);
        $form = $builder->getForm(); $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { $em->persist($item); $em->flush(); $this->addFlash('success', 'Registro salvo com sucesso.'); return $this->redirectToRoute('admin_data_index', ['tipo' => $tipo]); }
        return $this->render('admin/data/form.html.twig', ['tipo' => $tipo, 'item' => $item, 'form' => $form]);
    }

    /** @return class-string */
    private function classFor(string $tipo): string { return self::MAP[$tipo] ?? throw $this->createNotFoundException('Tipo de dado inválido.'); }
}
