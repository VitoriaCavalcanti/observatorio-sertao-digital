<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage', methods: ['GET'])]
    public function index(Request $request): Response
    {
        if ('admin.observatorio.localhost' === $request->getHost()) {
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('default/homepage.html.twig');
    }
}
