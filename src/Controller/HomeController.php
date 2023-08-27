<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\APIGOUVService;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(APIGOUVService $APIGOUVService): Response
    {
        dd($APIGOUVService->getResult());

        return $this->render('templates/base.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
