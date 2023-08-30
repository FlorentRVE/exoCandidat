<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\APIGOUVService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EntreprisesReuRepository;
use Symfony\Component\HttpFoundation\Request;

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
    }



// ...

#[Route('/tableau', name: 'app_tableau')]
public function getTableau(Request $request, EntreprisesReuRepository $entreprise): Response
{
    $searchTerm = $request->query->get('search');
    
    $dataAll = $entreprise->findAll();
    $data = [];

    foreach ($dataAll as $item) {
        // Si le terme de recherche est vide ou si le nom de l'entreprise contient le terme de recherche
        // on ajoute l'entreprise à la liste à afficher
        if (empty($searchTerm) || stripos($item->getNom(), $searchTerm) !== false) {
            $data[] = [
                'Nom' => $item->getNom(),
                'Dirigeant' => $item->getDirigeant(),
            ];
        }
    }

    return $this->render('display.html.twig', [
        'controller_name' => 'APIController',
        'data' => $data,
        'searchTerm' => $searchTerm,
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
