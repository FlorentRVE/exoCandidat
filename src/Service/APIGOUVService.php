<?php

namespace App\Service;
 
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\EntreprisesReuRepository;
use App\Entity\EntreprisesReu;
use Doctrine\ORM\EntityManagerInterface;
use Error;

class APIGOUVService
{
 
    public function __construct(private HttpClientInterface $client)
    {
    }
 
    //Récupération des datas
    public function getResult(): array 
    {
        $response = $this->client->request(
            'GET',
            'https://recherche-entreprises.api.gouv.fr/search?categorie_entreprise=PME%2CETI&departement=974&region=04&per_page=25',
        );

        // Gestion des erreurs
        $statusCode = $response->getStatusCode();
        // $statusCode = 200

        if (200 !== $statusCode) {
            throw new Error('Une erreur est survenue', $statusCode);
        }

        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        $content = $content["results"];

        // $nameList=[];

        // foreach($content as $value) {
        //     array_push($nameList, $value["nom_complet" ]);
        // }

        $boiteList=[];

        foreach($content as $value) {

            $name= $value["nom_complet" ];
            $dirigeant= $value["dirigeants" ];

            $entreprise = [];
            $entreprise = ['nom_entreprise' => $name, 'dirigeant' => $dirigeant];

            if(isset($dirigeant[0])) {
                $entreprise = ['nom_entreprise' => $name, 'dirigeant' => $dirigeant[0]];
            } else {
                $entreprise = ['nom_entreprise' => $name, 'dirigeant' => ''];
            }

            array_push($boiteList, $entreprise);
        }
        

        return $boiteList;
    }

    // Upload des datas dans la BDD
    public function uploadResult(EntreprisesReuRepository $entreprise, EntityManagerInterface $entityManager)
    {

        // Récupération et suppression de la table
        $entrepriseAll = $entreprise->findAll();

        foreach($entrepriseAll as $value) {
            $entityManager->remove($value);
            $entityManager->flush();
        }

        // Rechargement de l'ID de la table
        $connection = $entityManager->getConnection();
        $connection->exec('ALTER TABLE entreprises_reu AUTO_INCREMENT = 1');

        // Récupération des entreprises et injection dans la table
        $response = $this->client->request(
            'GET',
            'https://recherche-entreprises.api.gouv.fr/search?categorie_entreprise=PME%2CETI&departement=974&region=04&per_page=25',
        );

        // Gestion des erreurs
        $statusCode = $response->getStatusCode();
        // $statusCode = 200

        if (200 !== $statusCode) {
            throw new Error('Une erreur est survenue', $statusCode);
        }

        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        $content = $content["results"];

        $name='';        

        foreach($content as $value) {

            $name= $value["nom_complet" ];
            $dirigeant=$value["dirigeants" ];            
            $entreprise = new EntreprisesReu();
            $entreprise->setNom($name);

            if(isset($dirigeant[0])) {
            $entreprise->setDirigeant($dirigeant[0]);
            }

            // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $entityManager->persist($entreprise);
            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
        }
            

        
    }

    // Suppression des datas dans la BDD (Seulement pour tests)
    public function deleteBDD(EntreprisesReuRepository $entreprise, EntityManagerInterface $entityManager) 
    {
        
        $entrepriseAll = $entreprise->findAll();

        foreach($entrepriseAll as $value) {
            $entityManager->remove($value);
            $entityManager->remove($value);
            $entityManager->flush();
        }         

        $connection = $entityManager->getConnection();
        $connection->exec('ALTER TABLE entreprises_reu AUTO_INCREMENT = 1');
    }



}