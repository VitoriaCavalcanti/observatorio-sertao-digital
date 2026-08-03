<?php
namespace App\Controller\Admin;

use App\Repository\IndicadorRepository;
use App\Repository\InstituicaoRepository;
use App\Repository\PostRepository;
use App\Repository\ProjetoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_EDITOR')]
final class DashboardController extends AbstractController
{
    #[Route('', name: 'admin_dashboard', methods: ['GET'])]
    public function index(InstituicaoRepository $instituicoes, ProjetoRepository $projetos, IndicadorRepository $indicadores, PostRepository $posts): Response
    {
        $pendentes = $instituicoes->count(['statusCadastro' => 'em_analise']) + $projetos->count(['statusCadastro' => 'em_analise']) + $indicadores->count(['statusCadastro' => 'em_analise']);
        return $this->render('admin/dashboard.html.twig', ['contagens' => ['Instituições' => $instituicoes->count(), 'Projetos' => $projetos->count(), 'Indicadores' => $indicadores->count(), 'Em análise' => $pendentes, 'Avisos' => $posts->count()]]);
    }
}
