import {afterCalculateByError, setErrorMessage} from "../../functions/calculate";

let myChartSingle;
const ANIMATE_DURATION = 5; // アニメーションさせる時の時間。単位は秒
let animation_processing = false;

export function doSingleCalculate() {
    let data = {
        participant_number: $('#cointoss_participant_number').val(),
        banker_prepared_change: $('#cointoss_banker_prepared_change').val(),
        participation_fee: $('#cointoss_participation_fee').val(),
        banker_budget_degree: $('#cointoss_banker_budget_degree').val(),
        random_seed: $('#cointoss_random_seed').val(),
        initial_setup_cost: $('#cointoss_initial_setup_cost').val(),
        facility_unit_cost: $('#cointoss_facility_unit_cost').val(),
        facility_unit: $('#cointoss_facility_unit').val(),
    };
    $.ajax({
        type: 'POST',
        url: '/api/calculate/single',
        data: data,
        format: 'json',
        success: function (data) {
            $('#chart_area_single').show();
            $('#chart_area_multi').hide();
            $('#chart_area_multi_child').hide();
            afterSingleCalculateBySuccess(data);
            let offset =  $('#chart_area_single').offset().top - $('.kt-pagetitle').offset().top -20;
            $("html,body").animate({scrollTop: offset});
        },
        error: function (data) {
            setErrorMessage($.parseJSON(data.responseText));
            afterCalculateByError('coin_tossing_spinner');
        }
    });
}

function afterSingleCalculateBySuccess(returned_data) {
    $('button.calculate').removeClass('disabled');
    $('#coin_tossing_spinner').hide();
    renderSingleResult(returned_data);
    let formatter = new Intl.NumberFormat('ja-JP');
    $('#single_report_start').html(formatter.format(returned_data.start));
    $('#single_report_end').html(formatter.format(returned_data.end));
    $('#single_report_tried_players').html(formatter.format(returned_data.tried_players));
    $('#single_report_result').html(returned_data.result);
    $('#single_report_cost').html(returned_data.cost);
    $('#single_report_roi').html(formatter.format(returned_data.roi));
}

function renderSingleResult(returned_data)
{
    let transition_data = getSingleTransitionData(returned_data);
    let ctx = document.getElementById('chart_single');
    if(myChartSingle) {
        myChartSingle.destroy();
        $('#chart_single').removeAttr('width');
    }

    let label_array = _.pluck(transition_data, 'label');
    let data_array = _.pluck(transition_data, 'cache');
    myChartSingle = new Chart(ctx, get_single_chart_option(label_array, data_array));
}

function getSingleTransitionData(returned_data)
{
    let transition_data = [];

    $.each(returned_data['transitions'],function(index,val) {
        if (val.cache) {
            transition_data.push(
                {
                    label: index,
                    cache: {
                        x: index,
                        y: val.cache
                    },
                }
            )
        }
    });
    return transition_data;
}

function get_single_chart_option(label, data) {
    return {
        type: 'line',
        data: {
            labels: label,
            datasets: [
                {
                    label: '# 胴元の残金',
                    data: data,
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
                    ticks: {
                        beginAtZero: false,
                        fontSize: 10,
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
                        labelString: 'プレイヤー'
                    }
                }]
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
        }
    }
}

export function animateChart(canvas_id)
{
    let target_chart;
    let data_value;
    if (canvas_id == 'chart_single') {
        target_chart = myChartSingle;
    } else {
        return;
    }

    if (animation_processing) {
        return;
    }
    animation_processing = true;
    // 描画を一度空にする
    let datasets = target_chart.data.datasets[0].data;
    target_chart.data.datasets[0].data = [];
    target_chart.update();
    let timeout = ANIMATE_DURATION * 1000 / datasets.length;

    let id = setInterval(function(){
        data_value = datasets.shift();
        if (data_value == undefined) {
            clearInterval(id);
            animation_processing = false;
            return;
        }

        target_chart.data.datasets[0].data.push(data_value);
        target_chart.update();
    }, timeout);

}
