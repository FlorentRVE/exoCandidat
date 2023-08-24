<?php

namespace App\Service;
 
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\EntreprisesReuRepository;
use App\Entity\EntreprisesReu;
use Doctrine\ORM\EntityManagerInterface;
 
class APIGOUVService
{
 
    public function __construct(private HttpClientInterface $client)
    {
    }
 
    public function getResult(): array 
    {
        $response = $this->client->request(
            'GET',
            'https://recherche-entreprises.api.gouv.fr/search?categorie_entreprise=PME%2CETI&departement=974&region=04',
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

    public function uploadResult(EntreprisesReuRepository $entreprise, EntityManagerInterface $entityManager)
    {
        $response = $this->client->request(
            'GET',
            'https://recherche-entreprises.api.gouv.fr/search?categorie_entreprise=PME%2CETI&departement=974&region=04',
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

        $name='';        

        foreach($content as $value) {

            $name= $value["nom_complet" ];            
            $entreprise = new EntreprisesReu();
            $entreprise->setNom($name);
        }
            

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($entreprise);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        
    }



}