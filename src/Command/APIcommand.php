<?php

// src/Command/CreateUserCommand.php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\APIGOUVService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EntreprisesReuRepository;

// Le nom de la commande vient après "php bin/console"
#[AsCommand(
    name: 'app:getapi',
    description: 'Upload des données dans la BDD',
    hidden: false,
    aliases: ['app:get-api']
    )]
class APIcommand extends Command
{
    protected static $defaultDescription = 'Upload des données dans la BDD'; //Description de la commande

    public function __construct(
        private APIGOUVService $APIGOUVService, // Récupération du service et modules pour gestion entité
        private EntityManagerInterface $entityManager,
        private EntreprisesReuRepository $entreprise
    ){
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output) // Execution de la fonction du service

    {

        // appel service
        $this->APIGOUVService->uploadResult($this->entreprise, $this->entityManager);

        $output->writeln("Résultats de l'API uploadé dans la BDD");


        return Command::SUCCESS;

    }

    protected function configure(): void
    {
        $this
            // --help pour avoir de l'aide
            ->setHelp('Cette commande permet de récupérer les données de API GOUV...')
        ;
    }
}