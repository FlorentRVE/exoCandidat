<?php

namespace App\Service;
 
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\EntreprisesReuRepository;
use App\Entity\EntreprisesReu;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
 
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

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        $content = $content["results"];

        $nameList=[];

        foreach($content as $value) {
            array_push($nameList, $value["nom_complet" ]);
        }
        

        return $nameList;
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

        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        $content = $content["results"];

        $name='';        

        foreach($content as $value) {

            $name= $value["nom_complet" ];            
            $entreprise = new EntreprisesReu();
            $entreprise->setNom($name);

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