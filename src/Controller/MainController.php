<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main_home')]
    public function index(): Response
    {
        return $this->render('main/main.html.twig');
    }

    #[Route('/about_us', name: 'main_about_us')]
    public function about_us(): Response
    {
        return $this->render('main/about_us.html.twig');
    }
}
