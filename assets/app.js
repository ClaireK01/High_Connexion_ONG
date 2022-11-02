import Chart from 'chart.js/auto';
import {LegendBottomMargin} from "chartjs-plugin-custom-legend"
import ChartDataLabels from 'chartjs-plugin-datalabels';


import Routing from "fos-router";
const routes = require('../public/js/fos_js_routes.json');
Routing.setRoutingData(routes);

Chart.register(ChartDataLabels, LegendBottomMargin(50));


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
            labels: ['1€', '2€', '3€', '4€', '5€', '6€', '< 6€'],
            datasets: [{
                label: 'Nombre de donateurs',
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
        plugins:[ChartDataLabels],
        options: {
            plugins: {
                datalabels: {
                    color:'#0f0f0f',
                    anchor: 'end',
                    align:'top',
                    formatter: Math.round,
                },
            }
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

    const pieChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: departements,
            datasets: [{
                label: 'Les 10 départements avec le plus de donateurs',
                data: montants,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(143, 150, 146, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(245, 40, 145, 0.2)',
                    'rgba(67, 43, 15, 0.2)',
                    'rgba(7, 34, 4, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(147, 0, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(143, 150, 146, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(245, 40, 145, 1)',
                    'rgba(67, 43, 15, 1)',
                    'rgba(7, 34, 4, 0.2)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(147, 0, 255, 1)',
                ],
                borderWidth: 1,

            }]
        },
        options: {
            plugins: {
                datalabels: {
                    anchor: 'end',
                    formatter: Math.round,
                }
            }
        }
    });
    Chart.overrides['pie'].plugins.legend.display = true;
}

