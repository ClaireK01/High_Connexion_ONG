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
    public function dashboard_api() : JsonResponse {

        $error = null;
        $db = new \PDO('mysql:host=localhost;dbname=high_connexion', "root", "");
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

        $dbc = mysqli_connect('localhost', 'root', "", "high_connexion");
        $dons1 = mysqli_query($dbc, 'SELECT COUNT(*) as number FROM dons WHERE montant = 1' )
                ->fetch_array()['number'];
        $dons2 = mysqli_query($dbc, 'SELECT COUNT(*) as number FROM dons WHERE montant = 2' )
            ->fetch_array()['number'];
        $dons3 = mysqli_query($dbc, 'SELECT COUNT(*) as number FROM dons WHERE montant = 3' )
            ->fetch_array()['number'];
        $dons4 = mysqli_query($dbc, 'SELECT COUNT(*) as number FROM dons WHERE montant = 4' )
            ->fetch_array()['number'];
        $dons5 = mysqli_query($dbc, 'SELECT COUNT(*) as number FROM dons WHERE montant = 5' )
            ->fetch_array()['number'];
        $dons6 = mysqli_query($dbc, 'SELECT COUNT(*) as number FROM dons WHERE montant = 6' )
            ->fetch_array()['number'];
        $dons6Plus = mysqli_query($dbc, 'SELECT COUNT(*) as number FROM dons WHERE montant > 6' )
            ->fetch_array()['number'];

       return $this->json([$dons1, $dons2, $dons3, $dons4, $dons5, $dons6, $dons6Plus], status: 200);
    }

    #[Route('/api_piechart', name: 'app_api_piechart', options:['expose'=>true])]
    public function api_pieChart(): JsonResponse
    {
        $dbc = mysqli_connect('localhost', 'root', "", "high_connexion");
        $datas = [];
        $departement = mysqli_query($dbc, '
         SELECT SUM(montant), SUBSTRING(code_postal, 1, 2) FROM users INNER JOIN dons d on users.telephone = d.telephone group by SUBSTRING(code_postal, 1, 2) ORDER BY SUM(montant) DESC LIMIT 10;
        ');

        foreach ($departement->fetch_all() as $row){
            $res = (object)array("montant_total" => $row[0], "departement"=>$row[1]);
            $datas [] = $res;
        }

        return $this->json($datas, status: 200);
    }
 }
