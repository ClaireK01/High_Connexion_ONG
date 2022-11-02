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
    name: 'insert-data-ong',
    description: 'Add a short description for your command',
)]
class InsertDataCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $handle = fopen('src/assets/contact.csv', 'r');
        $line = 1;
        while (($row = fgetcsv($handle, null, ';')) !== false) {
            if ($line > 1) {
                $this->getDataAndInsert($row);
                dump($row);
            }
            $line++;
        }
        fclose($handle);


        $io->success('Toutes les données on été insérés!');

        return Command::SUCCESS;
    }

    function getDataAndInsert($datas):void
    {

        $date = $datas[0];
        $montant = intval($datas[1]);
        $tel = strval($datas[2]);
        $code_postal = $datas[3];

        $this->insertDataCodePostal($code_postal, $tel);
        $this->insertDataDons($montant, $tel, $date);

    }

    //Insertion table dons
    function insertDataDons($montant, $tel, $date):void
    {

        $dbh = new \PDO('mysql:host=localhost;dbname=high_connexion', "root", "");
        $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

        try {
            //Insertion des nouvelles données
            $preparedQuery = $dbh->prepare('INSERT INTO dons (telephone, montant, last_donated_at) VALUES (:telephone, :montant, :date)');
            $preparedQuery->bindParam(':date', $date);
            $preparedQuery->bindParam(':montant', $montant);
            $preparedQuery->bindParam(':telephone', $tel);
            $preparedQuery->execute();

        } catch (\PDOException $e) {
            //Si telephone déja enregistré, on met le montant à jour et prend la date de dons la plus récente
            try {
                dump("L'utilisateur est déjà présent dans la base de donnée. Mis à jour du montant.");
                $preparedQuery = $dbh->prepare('UPDATE dons SET montant = (montant + :montant), 
                    last_donated_at = CASE WHEN last_donated_at < :date THEN :date2 ELSE last_donated_at END
                    WHERE telephone = :telephone;
                    ');
                $preparedQuery->bindParam(':date', $date);
                $preparedQuery->bindParam(':date2', $date);
                $preparedQuery->bindParam(':montant', $montant);
                $preparedQuery->bindParam(':telephone', $tel);
                $preparedQuery->execute();

            } catch (\PDOException $ex) {
                dd($ex->getMessage());
            }
        }
    }

    //Insertion table users
    function insertDataCodePostal($code, $tel):void
    {
        $dbh = new \PDO('mysql:host=localhost;dbname=high_connexion', "root", "");
        $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

        try {
            $preparedQuery = $dbh->prepare("INSERT INTO users (code_postal, telephone) VALUES (?, ?)");
            $preparedQuery->execute([$code, $tel]);
        } catch (\PDOException $e) {
            dump($e->getMessage());
        }
    }
}
