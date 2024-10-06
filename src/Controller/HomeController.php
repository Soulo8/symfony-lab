<?php

namespace App\Controller;

use SlopeIt\BreadcrumbBundle\Attribute\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    #[Breadcrumb([
        ['label' => 'home'],
    ])]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }
}
