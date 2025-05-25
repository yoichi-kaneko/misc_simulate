import {afterCalculateByError, setErrorMessage} from "./calculate";
import {notifyComplete} from "./notify";
import {Chart, registerables} from "chart.js";
Chart.register(...registerables);

let myChartNashSimulation;

export function doNashCalculate()
{
    const data = {
        alpha_1: {
            numerator: $('#alpha_1_numerator').val(),
            denominator: $('#alpha_1_denominator').val(),
        },
        alpha_2: {
            numerator: $('#alpha_2_numerator').val(),
            denominator: $('#alpha_2_denominator').val(),
        },
        beta_1: {
            numerator: $('#beta_1_numerator').val(),
            denominator: $('#beta_1_denominator').val(),
        },
        beta_2: {
            numerator: $('#beta_2_numerator').val(),
            denominator: $('#beta_2_denominator').val(),
        },
        rho: {
            numerator: $('#rho_numerator').val(),
            denominator: $('#rho_denominator').val(),
        },
    };
    $.ajax({
        type: 'POST',
        url: '/api/nash/calculate',
        data: data,
        success: function (data) {
            $('button.calculate').removeClass('disabled');
            renderNashSimulationChart(data.render_params);
            $('#nash_spinner').hide();
            notifyComplete();
        },
        error: function (data) {
            setErrorMessage($.parseJSON(data.responseText));
            let offset =  $('#alert_danger').offset().top - $('.kt-pagetitle').offset().top -20;
            $("html,body").animate({scrollTop: offset});
            afterCalculateByError('nash_spinner');
        }
    });
}

/**
 * チャートの出力を行う
 * @param render_params
 */
function renderNashSimulationChart(render_params)
{
    $('#chart_area_nash').show();
    let ctx_simulation = document.getElementById('chart_nash_simulation');
    if(myChartNashSimulation) {
        myChartNashSimulation.destroy();
        $('#chart_nash_simulation').removeAttr('width');
    }
    myChartNashSimulation = new Chart(
        ctx_simulation,
        getNashSimulationOption(
            render_params
        )
    );
}

/**
 * チャートのパラメータ生成を行う
 * @param render_params
 */
function getNashSimulationOption(render_params)
{
    const border_color = '#324463';

    let data_array = [];
    $.each(render_params, function(index, val) {
        data_array.push({
            title: val.title,
            display_text: val.display_text,
            x: val.x,
            y: val.y,
        });
    });

    const datasets = [
        {
            type: 'line',
            label: 'Nash',
            data: data_array,
            borderColor: border_color,
            backgroundColor: border_color,
            segment: {
                borderDash: function(context) {
                    if (context.p1.raw.title === 'beta' || context.p0.raw.title === 'alpha') {
                        return [5, 5];
                    }
                    return undefined;
                }
            },
            borderWidth: 2,
            lineTension: 0,
            pointRadius: 2,
            fill: false,
            showLine: true
        }
    ];
    return {
        type: "scatter",
        data: {
            datasets: datasets,
        },
        options: {
            responsive: true,
            animation: false,
            scales: {
                y: {
                    position: "left",
                    beginAtZero: true,
                    min: 0,
                    // max: max_y_axes,
                    fontSize: 10,
                    title: {
                        display: false,
                    }
                },
                x: {
                    beginAtZero: true,
                    fontSize: 11,
                    title: {
                        display: false,
                    }
                }
            },
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    mode: 'index',
                    displayColors: false,
                    intersect: false,
                    callbacks: {
                        title: function(tooltipItems) {
                            return tooltipItems[0].raw.title + ':';
                        },
                        label: function(tooltipItem) {
                            return tooltipItem.dataset.data[tooltipItem.dataIndex].display_text;
                        }
                    }
                }
            }
        },
        plugins: [
            {
                beforeDraw: drawBackground
            },
        ]
    }
}

function drawBackground(target) { // 引数はmyChart自身とされる。
    let cvs = document.getElementById(target.canvas.id); // もちろん'my_graph'で直接指定してもOK
    let ctx = cvs.getContext('2d');

    // プロット領域に重なるように、背景色の四角形を描画
    ctx.fillStyle = "white";              // 背景色（今回は濃いグレー）
    ctx.fillRect(0, 0, target.width, target.height);
}
