<?php
namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/usuarios')]
#[IsGranted('ROLE_ADMIN')]
final class UserController extends AbstractController
{
    #[Route('', name: 'admin_user_index', methods: ['GET'])]
    public function index(UserRepository $repository): Response { return $this->render('admin/user/index.html.twig', ['usuarios' => $repository->findBy([], ['nome' => 'ASC'])]); }
    #[Route('/novo', name: 'admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response { return $this->form($request, new User(), $em, $hasher); }
    #[Route('/{id}/editar', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response { return $this->form($request, $user, $em, $hasher); }
    private function form(Request $request, User $user, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createFormBuilder($user)->add('nome')->add('email')->add('roles', ChoiceType::class, ['multiple' => true, 'expanded' => true, 'choices' => ['Usuário' => User::ROLE_USER, 'Editor' => User::ROLE_EDITOR, 'Administrador' => User::ROLE_ADMIN]])->add('novaSenha', PasswordType::class, ['mapped' => false, 'required' => !$user->getId(), 'label' => 'Senha'])->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { $senha = $form->get('novaSenha')->getData(); if ($senha) $user->setPassword($hasher->hashPassword($user, $senha)); $em->persist($user); $em->flush(); $this->addFlash('success', 'Usuário salvo.'); return $this->redirectToRoute('admin_user_index'); }
        return $this->render('admin/user/form.html.twig', ['form' => $form, 'usuario' => $user]);
    }
}
