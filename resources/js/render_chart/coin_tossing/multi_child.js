import {afterCalculateByError, setErrorMessage} from "../../functions/calculate";

let myChartMultiChild;

export function doMultiChildCalculate() {
    let data = {
        multi_step: $('#multi_step').val(),
        child_x_label: $('#child_x_label').val()
    };
    $.ajax({
        type: 'POST',
        url: '/api/calculate/multi_child',
        data: data,
        format: 'json',
        success: function (data) {
            if (data.result == 'ng') {
                $('#multi_child_alert_danger_message').html('データの取得に失敗しました。データが存在しない範囲の可能性があります');
                $('#multi_child_alert_danger').show();
                return;
            }
            $('#chart_area_multi_child').show();
            renderMultiChildResult(data);
            let offset =  $('#chart_area_multi_child').offset().top - $('.kt-pagetitle').offset().top -20;
            $("html,body").animate({scrollTop: offset});
        },
        error: function (data) {
            setErrorMessage($.parseJSON(data.responseText));
            afterCalculateByError('coin_tossing_spinner');
        }
    });
}

function renderMultiChildResult(returned_data)
{
    let transition_data = getSingleTransitionData(returned_data);
    let ctx = document.getElementById('chart_multi_child');
    if(myChartMultiChild) {
        myChartMultiChild.destroy();
        $('#chart_multi_child').removeAttr('width');
    }

    let label_array = _.pluck(transition_data, 'label');
    let data_array = _.pluck(transition_data, 'cache');
    myChartMultiChild = new Chart(ctx, get_single_chart_option(label_array, data_array));
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
