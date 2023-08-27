<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\APIGOUVService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EntreprisesReuRepository;

class APIController extends AbstractController
{

    private $apiGouvService;

    public function __construct(APIGOUVService $apiGouvService)
    {
        $this->apiGouvService = $apiGouvService;
    }

    #[Route('/home', name: 'app_home')] //Récupération des datas
    public function getData(): Response
    {
        dd($this->apiGouvService->getResult());

        return $this->render('templates/base.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/upload', name: 'app_upload')] // Upload des datas dans la BDD
    public function uploadData(EntreprisesReuRepository $entreprise, EntityManagerInterface $entityManager): Response
    {
        // Appel de la fonction d'upload du service
        $this->apiGouvService->uploadResult($entreprise, $entityManager);

        return new Response('Upload effectué avec succès !');
    }

    #[Route('/delete', name: 'app_delete')] // Suppression des datas dans la BDD
    public function deleteData(EntreprisesReuRepository $entreprise, EntityManagerInterface $entityManager): Response
    {
        // Appel de la fonction de suppression du service
        $this->apiGouvService->deleteBDD($entreprise, $entityManager);

        return new Response('Suppression effectué avec succès !');
    }
}
