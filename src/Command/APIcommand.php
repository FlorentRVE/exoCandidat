<?php

// src/Command/CreateUserCommand.php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\APIGOUVService;

// the name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'app:getapi',
    description: 'Get data from API.',
    hidden: false,
    aliases: ['app:get-api']
    )]
class APIcommand extends Command
{
    protected static $defaultDescription = 'Get data from API'; //Description de la commande

    public function __construct(
        private APIGOUVService $APIGOUVService, // Récupération du service
    ){
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output) // Execution de la fonction du service

    {

        // appel service
        $this->APIGOUVService->getResult();

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