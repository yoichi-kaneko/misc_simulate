import {afterCalculateByError, setErrorMessage} from "./calculate";
import {notifyComplete} from "./notify";
import {Chart, registerables} from "chart.js";
import {htmlLegendPlugin} from "../chartjs/plugins/html_legend.js";
import showMore from '../plugins/show-more-wrapper.js';

Chart.register(...registerables);

let myChartCentipedeSimulation;

export function doCentipedeCalculate()
{
    let patterns = {
        a_1: {
            base_numerator: $('#base_numerator_a_1').val(),
            numerator_exp_1: $('#numerator_exp_1_a_1').val(),
            numerator_exp_2: $('#numerator_exp_2_a_1').val(),
            denominator_exp: $('#denominator_exp_a_1').val(),
        }
    };
    if ($('input#simulate_combination').prop('checked')) {
        patterns['a_2'] = {
            base_numerator: $('#base_numerator_a_2').val(),
            numerator_exp_1: $('#numerator_exp_1_a_2').val(),
            numerator_exp_2: $('#numerator_exp_2_a_2').val(),
            denominator_exp: $('#denominator_exp_a_2').val(),
        };
    }
    if ($('input#enable_pattern_b').prop('checked')) {
        patterns['b_1'] = {
            base_numerator: $('#base_numerator_b_1').val(),
            numerator_exp_1: $('#numerator_exp_1_b_1').val(),
            numerator_exp_2: $('#numerator_exp_2_b_1').val(),
            denominator_exp: $('#denominator_exp_b_1').val(),
        };
    }
    if ($('input#simulate_combination').prop('checked') && $('input#enable_pattern_b').prop('checked')) {
        patterns['b_2'] = {
            base_numerator: $('#base_numerator_b_2').val(),
            numerator_exp_1: $('#numerator_exp_1_b_2').val(),
            numerator_exp_2: $('#numerator_exp_2_b_2').val(),
            denominator_exp: $('#denominator_exp_b_2').val(),
        };
    }

    let data = {
        patterns: patterns,
        max_step: $('#max_step').val(),
        max_rc: $('#max_rc').val(),
    };
    if ($('input#simulate_combination').prop('checked')) {
        let combination_player_1 = {
            a: $('input:radio[name="combination_player_1_a"]:checked').val(),
        };
        if ($('input#enable_pattern_b').prop('checked')) {
            combination_player_1['b'] = $('input:radio[name="combination_player_1_b"]:checked').val();
        }
        data['combination_player_1'] = combination_player_1;
    }
    $.ajax({
        type: 'POST',
        url: '/api/centipede/calculate',
        data: data,
        format: 'json',
        success: function (data) {
            renderCentipedeReportArea(data.pattern_data, data.combination_data);
            renderCentipedeSimulationChart(
                data.render_params,
                data.pattern_data,
                data.combination_data
            );

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
function renderCentipedeReportArea(pattern_data, combination_data)
{
    // レポートデータの生成
    $('#centipede_result').html('');

    $.each(pattern_data, function(index,val) {
        let tmpl = $('#centipedeResultTemplate').render({
            pattern: index,
            table_data: val.data,
            cognitive_unit_value: val.cognitive_unit_value,
            cognitive_unit_latex_text: val.cognitive_unit_latex_text,
            average_of_reversed_causality: val.average_of_reversed_causality,
        });
        $('#centipede_result').append(tmpl);
    });
    if (combination_data) {
        $.each(combination_data, function(index,val) {
            let tmpl = $('#centipedeCombinationResultTemplate').render({
                pattern: index,
                table_data: val.data,
                cognitive_unit_value_1: val.cognitive_unit_value_1,
                cognitive_unit_value_2: val.cognitive_unit_value_2,
                cognitive_unit_latex_text_1: val.cognitive_unit_latex_text_1,
                cognitive_unit_latex_text_2: val.cognitive_unit_latex_text_2,
                average_of_reversed_causality: val.average_of_reversed_causality,
            });
            $('#centipede_result').append(tmpl);
        });
    }

    // 切り替えタブの生成
    let tmpl = $('#centipedeTabTemplate').render({
        pattern_data: pattern_data,
        combination_data: combination_data,
    });
    $('#centipede_tab').html(tmpl);
    // レポートのタブ切り替えをバインド
    $('#centipede_tab .switch_pattern').click(function () {
        let pattern = $(this).attr('pattern');
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
            showMore.init('#' + id, {minheight: 300});
        }
    });
    $('#centipede_tab .switch_pattern:first').addClass('active');
    $('#centipede_result .report_block').not(':first').hide();
}

/**
 * チャートの出力を行う
 * @param render_params
 * @param pattern_data
 * @param combination_data
 */
function renderCentipedeSimulationChart(render_params, pattern_data, combination_data)
{
    let ctx_simulation = document.getElementById('chart_centipede_simulation');
    if(myChartCentipedeSimulation) {
        myChartCentipedeSimulation.destroy();
        $('#chart_centipede_simulation').removeAttr('width');
    }
    myChartCentipedeSimulation = new Chart(
        ctx_simulation,
        getCentipedeSimulationOption(
            render_params,
            pattern_data,
            combination_data
        )
    );
}

/**
 * チャートのパラメータ生成を行う
 * @param render_params
 * @param pattern_data
 * @param combination_data
 */
function getCentipedeSimulationOption(render_params, pattern_data, combination_data)
{
    let base_key = 'a_1';
    let chart_data = pattern_data[base_key].chart_data;
    let datasets = [];
    let label_array = [];
    let border_color_list = [
        '#324463',
        '#5B93D3',
        '#7CBDDF',
        '#17A2B8',
        '#DC3545',
        '#6F42C1'
    ];
    let border_color = '';

    $.each(pattern_data, function(pattern, val) {
        chart_data = val.chart_data;
        let data_array = [];
        $.each(chart_data, function(index, val2) {
            data_array.push({
                x: val2.x,
                y: val2.y,
            });
        });
        border_color = border_color_list.shift();

        let dataset = {
            type: 'line',
            label: 'Pattern ' + pattern.toUpperCase(),
            data: data_array,
            borderColor: border_color,
            backgroundColor: border_color,
            borderWidth: 2,
            lineTension: 0,
            pointRadius: 0,
            fill: false
        };
        datasets.push(dataset);
    });
    $.each(pattern_data[base_key].chart_data, function(index, val) {
        label_array.push(val.x);
    });

    if (combination_data) {
        $.each(combination_data, function(combination_pattern, val) {
            chart_data = val.chart_data;
            let data_array = [];
            $.each(chart_data, function(index, val) {
                data_array.push({
                    x: val.x,
                    y: val.y,
                });
            });

            border_color = border_color_list.shift();

            let dataset = {
                type: 'line',
                label: 'Combination ' + combination_pattern.toUpperCase(),
                data: data_array,
                borderColor: border_color,
                backgroundColor: border_color,
                borderWidth: 2,
                lineTension: 0,
                pointRadius: 0,
                fill: false
            };
            datasets.push(dataset);
        });
    }

    // RCの最大値が指定されていればその値を、指定されていない場合はステップ数をy軸最大値にする
    let max_y_axes = render_params.max_step;
    if (render_params.max_rc) {
        max_y_axes = render_params.max_rc;
    }

    return {
        data: {
            labels: label_array,
            datasets: datasets,
        },
        options: {
            responsive: true,
            animation: false,
            scales: {
                y: {
                    type: "linear",
                    position: "left",
                    beginAtZero: true,
                    min: 0,
                    max: max_y_axes,
                    fontSize: 10,
                    title: {
                        display: true,
                        text: '#RC',
                        font: {
                            size: 15
                        }
                    }
                },
                x: {
                    beginAtZero: true,
                    fontSize: 11,
                    title: {
                        labelString: 'k',
                        display: false,
                    }
                }
            },
            plugins: {
                htmlLegend: {
                    // ID of the container to put the legend in
                    containerID: 'legend-container',
                },
                legend: {
                    display: false,
                },
                tooltip: {
                    mode: 'index',
                    displayColors: false,
                    intersect: false,
                    callbacks: {
                        title: function(tooltipItems) {
                            return 'k: ' + tooltipItems[0].label;
                        },
                        label: function(tooltipItem) {
                            let label = tooltipItem.dataset.label || '';

                            if (label) {
                                label += ': ';
                            }
                            label += tooltipItem.dataset.data[tooltipItem.dataIndex].y;
                            return label;
                        }
                    }
                }
            }
        },
        plugins: [
            {
                beforeDraw: drawBackground
            },
            htmlLegendPlugin
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