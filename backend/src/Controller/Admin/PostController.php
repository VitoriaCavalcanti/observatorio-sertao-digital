<?php
namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/avisos')]
#[IsGranted('ROLE_EDITOR')]
final class PostController extends AbstractController
{
    #[Route('', name: 'admin_post_index', methods: ['GET'])]
    public function index(PostRepository $repository): Response { return $this->render('admin/post/index.html.twig', ['posts' => $repository->findBy([], ['fixado' => 'DESC', 'atualizadoEm' => 'DESC'])]); }
    #[Route('/novo', name: 'admin_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, #[CurrentUser] User $user, EntityManagerInterface $em): Response { $post = (new Post())->setAutor($user); return $this->handle($request, $post, $em); }
    #[Route('/{id}/editar', name: 'admin_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $em): Response { return $this->handle($request, $post, $em); }
    #[Route('/{id}/excluir', name: 'admin_post_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Post $post, EntityManagerInterface $em): Response { if ($this->isCsrfTokenValid('delete-post-'.$post->getId(), (string) $request->request->get('_token'))) { $em->remove($post); $em->flush(); $this->addFlash('success', 'Aviso excluído.'); } return $this->redirectToRoute('admin_post_index'); }
    private function handle(Request $request, Post $post, EntityManagerInterface $em): Response { $form = $this->createForm(PostType::class, $post); $form->handleRequest($request); if ($form->isSubmitted() && $form->isValid()) { $em->persist($post); $em->flush(); $this->addFlash('success', 'Aviso salvo com sucesso.'); return $this->redirectToRoute('admin_post_index'); } return $this->render('admin/post/form.html.twig', ['form' => $form, 'post' => $post]); }
}
