import Chart from 'chart.js/auto';

import Routing from "fos-router";
const routes = require('../public/js/fos_js_routes.json');
Routing.setRoutingData(routes);


Routing.setRoutingData(routes);

const ctx = document.querySelector('#graph1').getContext('2d');
const ctx2 = document.querySelector('#graph2').getContext('2d');

$.ajax({
    url: Routing.generate('app_api_barchart'),
    success: function(response){
        showBarChart(response);
    },
    error:function (){
      window.alert('Un problème est survenu');
    }
})

$.ajax({
    url: Routing.generate('app_api_piechart'),
    success: function(response){
        showPieChart(response);
    },
    error:function (){
        window.alert('Un problème est survenu');
    }
})


function showBarChart(datas){

    const barChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['1', '2', '3', '4', '5', '6', '< 6'],
            datasets: [{
                label: 'Montant en €',
                data: datas,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1,

            }]
        },
        options: {
        }
    });

}

function showPieChart(datas){
    // let montant = datas.filter((word) => word.montant_total);
    let montants = [];
    let departements = [];
    datas.forEach(data =>{
        montants.push(data.montant_total);
        departements.push(data.departement);
    })
    console.log(montants);
    console.log(departements);

    const pieChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: departements,
            datasets: [{
                label: 'Nombre de donneurs',
                data: montants,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1,

            }]
        },
        options: {
        }
    });
}

//TODO: ajouter + de couleurs pour piechart + améliorer légendes + faire apparaitre nombre sur chart


