import {afterCalculateByError, setErrorMessage} from "./calculate";
import {notifyComplete} from "./notify";

let myChartCentipedeSimulation;

export function doCentipedeCalculate()
{
    let patterns = {
        a: {
            base_numerator: $('#base_numerator_a').val(),
            numerator_exp_1: $('#numerator_exp_1_a').val(),
            numerator_exp_2: $('#numerator_exp_2_a').val(),
            denominator_exp: $('#denominator_exp_a').val(),
        }
    };
    if ($('input#enable_pattern_b').prop('checked')) {
        patterns['b'] = {
            base_numerator: $('#base_numerator_b').val(),
            numerator_exp_1: $('#numerator_exp_1_b').val(),
            numerator_exp_2: $('#numerator_exp_2_b').val(),
            denominator_exp: $('#denominator_exp_b').val(),
        };
    }
    let data = {
        patterns: patterns,
        chart_offset: $('#chart_offset').val(),
        max_step: $('#max_step').val()
    };
    $.ajax({
        type: 'POST',
        url: '/api/centipede/calculate',
        data: data,
        format: 'json',
        success: function (data) {
            renderCentipedeReportArea(data.pattern_data);
            renderCentipedeSimulationChart(data.pattern_data.a.chart_data);

            $('button.calculate').removeClass('disabled');
            $('#centipede_spinner').hide();
            notifyComplete();
        },
        error: function (data) {
            setErrorMessage($.parseJSON(data.responseText));
            let offset =  $('#alert_danger').offset().top - $('.kt-pagetitle').offset().top -20;
            $("html,body").animate({scrollTop: offset});
            afterCalculateByError('centipede_spinner');
        }
    });
}

/**
 * レポートエリアの描画を行う
 * @param data
 */
function renderCentipedeReportArea(pattern_data)
{
    // レポートデータの生成
    $('#centipede_result').html('');

    $.each(pattern_data, function(index,val) {
        let tmpl = $('#centipedeResultTemplate').render({
            pattern: index,
            table_data: val.data,
            cognitive_unit_value: val.cognitive_unit_value,
            cognitive_unit_latex_text: val.cognitive_unit_latex_text,
        });
        $('#centipede_result').append(tmpl);
    });

    // 切り替えタブの生成
    let tmpl = $('#centipedeTabTemplate').render({
        pattern_data: pattern_data,
    });
    $('#centipede_tab').html(tmpl);
    // レポートのタブ切り替えをバインド
    $('#centipede_tab .switch_pattern').click(function () {
        let pattern = $(this).attr('pattern');
        console.log(pattern);
        $('#centipede_result .report_block').each(function () {
            let id = 'report_pattern_' + pattern;
            if ($(this).attr('id') == id) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $('#centipede_result .katex_exp').each(function () {
        let element = $(this)[0];
        katex.render($(this).attr('expression'), element, {
            throwOnError: false
        });
    });
    $('#chart_area_centipede').show();

    $('.showmore_block').each(function() {
        let id = $(this).attr('id');
        if ($('#showmore-' + id).length == 0) {
            $('#' + id).showMore({
                minheight: 300
            });
        }
    });
    $('#centipede_result .report_block').not(':first').hide();
}

/**
 * チャートの出力を行う
 * @param data
 */
function renderCentipedeSimulationChart(chart_data)
{
    let ctx_simulation = document.getElementById('chart_centipede_simulation');
    if(myChartCentipedeSimulation) {
        myChartCentipedeSimulation.destroy();
        $('#chart_centipede_simulation').removeAttr('width');
    }
    myChartCentipedeSimulation = new Chart(ctx_simulation, getCentipedeSimulationOption(chart_data));
}

/**
 * チャートのパラメータ生成を行う
 * @param chart_data
 */
function getCentipedeSimulationOption(chart_data)
{
    let label_array = [];
    let data_array = [];

    $.each(chart_data, function(index,val) {
        label_array.push(val.x);
        data_array.push({
            x: val.x,
            y: val.y,
        });
    });

    return {
        data: {
            labels: label_array,
            datasets: [
                {
                    type: 'line',
                    yAxisID: 'y-axis-1',
                    label: 'x-axis-#k / y-axis-#CF',
                    data: data_array,
                    borderColor: '#324463',
                    borderWidth: 2,
                    lineTension: 0,
                    pointRadius: 0,
                    fill: false
                },
            ]
        },
        options: {
            responsive: true,
            animation: false,
            legend: {
                display: true,
                labels: {
                    display: true
                }
            },
            scales: {
                yAxes: [{
                    id: "y-axis-1",
                    type: "linear",
                    position: "left",
                    ticks: {
                        beginAtZero: true,
                        min: 0,
                        fontSize: 10,
                    },
                    scaleLabel: {
                        display: true,
                        fontSize: 15,
                        labelString: '#CF'
                    }
                }],
                xAxes: [{
                    ticks: {
                        beginAtZero: true,
                        fontSize: 11,
                    },
                    scaleLabel: {
                        display: true,
                        fontSize: 15,
                        labelString: '#k'
                    }
                }]
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(tooltipItem, data) {
                        let label = '#CF: ';
                        label += data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].y;
                        return label;
                    }
                }
            },
        }
    }
}
