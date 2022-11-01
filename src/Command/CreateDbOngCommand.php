<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'create-db-ong',
    description: 'Add a short description for your command',
)]
class CreateDbOngCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);

        $dbh = new \PDO('mysql:host=localhost', "root", "");

        $dbh->exec("CREATE DATABASE high_connexion");
        $dbh->exec("USE high_connexion;
            CREATE TABLE users(
            telephone VARCHAR(10) PRIMARY KEY UNIQUE NOT NULL,
            code_postal VARCHAR(5)
            );
            ALTER TABLE users ADD CONSTRAINT users UNIQUE(code_postal, telephone);
            ");

        $dbh->exec("USE high_connexion;
              CREATE TABLE dons(
              id INT PRIMARY KEY AUTO_INCREMENT,
              telephone VARCHAR(10) UNIQUE,
              FOREIGN KEY (telephone) REFERENCES users(telephone),
              montant INT,
              last_donated_at DATETIME
              )
              ");


        $io->success('La base à été créé avec succès!');
        return Command::SUCCESS;

    }
}
