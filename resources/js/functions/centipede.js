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
    if ($('input#simulate_union_mode').prop('checked')) {
        patterns['b'] = {
            base_numerator: $('#base_numerator_b').val(),
            numerator_exp_1: $('#numerator_exp_1_b').val(),
            numerator_exp_2: $('#numerator_exp_2_b').val(),
            denominator_exp: $('#denominator_exp_b').val(),
        };
    }

    let data = {
        patterns: patterns,
        max_step: $('#max_step').val(),
        max_rc: $('#max_rc').val(),
    };
    if ($('input#simulate_union_mode').prop('checked')) {
        data['union_player_1'] = $('input:radio[name="union_player_1"]:checked').val();
    }
    $.ajax({
        type: 'POST',
        url: '/api/centipede/calculate',
        data: data,
        format: 'json',
        success: function (data) {
            renderCentipedeReportArea(data.pattern_data, data.union_data);
            renderCentipedeSimulationChart(
                data.render_params,
                data.pattern_data,
                data.union_data
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
function renderCentipedeReportArea(pattern_data, union_data)
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
    if (union_data) {
        let tmpl = $('#centipedeUnionResultTemplate').render({
            table_data: union_data.data,
            cognitive_unit_value_a: union_data.cognitive_unit_value_a,
            cognitive_unit_value_b: union_data.cognitive_unit_value_b,
            cognitive_unit_latex_text_a: union_data.cognitive_unit_latex_text_a,
            cognitive_unit_latex_text_b: union_data.cognitive_unit_latex_text_b,
            average_of_reversed_causality: union_data.average_of_reversed_causality,
        });
        $('#centipede_result').append(tmpl);
    }

    // 切り替えタブの生成
    let tmpl = $('#centipedeTabTemplate').render({
        pattern_data: pattern_data,
        union_data: union_data,
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
            $('#' + id).showMore({
                minheight: 300
            });
        }
    });
    $('#centipede_tab .switch_pattern:first').addClass('active');
    $('#centipede_result .report_block').not(':first').hide();
}

/**
 * チャートの出力を行う
 * @param render_params
 * @param pattern_data
 * @param union_data
 */
function renderCentipedeSimulationChart(render_params, pattern_data, union_data)
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
            union_data
        )
    );
}

/**
 * チャートのパラメータ生成を行う
 * @param render_params
 * @param pattern_data
 * @param chart_data
 */
function getCentipedeSimulationOption(render_params, pattern_data, union_data)
{
    let chart_data = pattern_data.a.chart_data;
    let datasets = [];
    let label_array = [];
    let border_color_list = [
        '#324463',
        '#5B93D3',
        '#DC3545'
    ];

    $.each(pattern_data, function(pattern, val) {
        chart_data = val.chart_data;
        let data_array = [];
        $.each(chart_data, function(index, val2) {
            data_array.push({
                x: val2.x,
                y: val2.y,
            });
        });

        let dataset = {
            type: 'line',
            label: 'Pattern ' + pattern.toUpperCase(),
            data: data_array,
            borderColor: border_color_list.shift(),
            borderWidth: 2,
            lineTension: 0,
            pointRadius: 0,
            fill: false
        };
        datasets.push(dataset);
    });
    $.each(pattern_data.a.chart_data, function(index, val) {
        label_array.push(val.x);
    });

    if (union_data) {
        chart_data = union_data.chart_data;
        let data_array = [];
        $.each(chart_data, function(index, val) {
            data_array.push({
                x: val.x,
                y: val.y,
            });
        });

        let dataset = {
            type: 'line',
            label: 'Union Mode',
            data: data_array,
            borderColor: border_color_list.shift(),
            borderWidth: 2,
            lineTension: 0,
            pointRadius: 0,
            fill: false
        };
        datasets.push(dataset);
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
            legend: {
                display: true,
                labels: {
                    display: true
                }
            },
            scales: {
                yAxes: [{
                    type: "linear",
                    position: "left",
                    ticks: {
                        beginAtZero: true,
                        min: 0,
                        max: max_y_axes,
                        fontSize: 10,
                    },
                    scaleLabel: {
                        display: true,
                        fontSize: 15,
                        labelString: '#RC'
                    }
                }],
                xAxes: [{
                    ticks: {
                        beginAtZero: true,
                        fontSize: 11,
                    },
                    scaleLabel: {
                        labelString: 'k',
                        display: false,
                    }
                }]
            },
            tooltips: {
                mode: 'index',
                displayColors: false,
                intersect: false,
                callbacks: {
                    title: function(tooltipItems) {
                        return 'k: ' + tooltipItems[0].xLabel;
                    },
                    label: function(tooltipItem, data) {
                        let label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label) {
                            label += ': ';
                        }
                        label += data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].y;
                        return label;
                    }
                }
            },
        }
    }
}
