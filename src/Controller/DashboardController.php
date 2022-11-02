<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/api_barchart', name: 'app_api_barchart',  options:['expose'=>true])]
    public function api_barChart() : JsonResponse {

        $dbc = mysqli_connect('localhost', 'root', "", "high_connexion");
        $datas = [];
        for ($i = 1; $i <= 7; $i ++){
            if($i < 6 ){
                $don = mysqli_query($dbc, 'SELECT COUNT(*) as number FROM dons WHERE montant = '. $i .';')
                    ->fetch_array()['number'];
                $datas [] = $don;
            }else{
                $don = mysqli_query($dbc, 'SELECT COUNT(*) as number FROM dons WHERE montant > 6 ;')
                    ->fetch_array()['number'];
                $datas [] = $don;
            }
        }

       return $this->json($datas, status: 200);
    }

    #[Route('/api_piechart', name: 'app_api_piechart', options:['expose'=>true])]
    public function api_pieChart(): JsonResponse
    {
        //Connexion db
        $dbc = mysqli_connect('localhost', 'root', "", "high_connexion");

        //Instanciation tableau de données
        $datas = [];

        // Requête des 10 premiers département
        $departement = mysqli_query($dbc, '
        SELECT COUNT(dons.telephone) as tel, SUBSTRING(code_postal, 1, 2) as cp FROM dons INNER JOIN users u on u.telephone = dons.telephone 
        GROUP BY cp ORDER BY tel DESC LIMIT 10;   
        ');

        //Requète pour la somme des autre departement en dehors des 10 premier:
        $somme_dep_restant = mysqli_query($dbc, '
        SELECT SUM(dep) as total_dep FROM (
        SELECT COUNT(telephone) as dep FROM users GROUP BY SUBSTRING(code_postal, 1, 2) ORDER BY dep DESC LIMIT 90 OFFSET 10
        ) as total;
        ');

        //Insertion resultats dans le tableau
        foreach ($departement->fetch_all() as $row){
            $res = (object)array("montant_total" => $row[0], "departement"=>$row[1]);
            $datas [] = $res;
        }
        $other_dep = (object)array("montant_total"=>$somme_dep_restant->fetch_array()['total_dep'], "departement"=>"Autres");
        $datas [] = $other_dep;

        return $this->json($datas, status: 200);
    }
 }
