import {
    clearProgressBar,
    getReturnedDataIfExist,
    startGettingProgress
} from "../../functions/progress";
import {afterCalculateByError, setErrorMessage} from "../../functions/calculate";

let tokenId;
let myChartMulti;
let multiDistributionData;

export function doMultiCalculate() {
    let data = {
        participant_number: $('#cointoss_participant_number').val(),
        banker_prepared_change: $('#cointoss_banker_prepared_change').val(),
        participation_fee: $('#cointoss_participation_fee').val(),
        banker_budget_degree: $('#cointoss_banker_budget_degree').val(),
        iteration: $('#cointoss_iteration').val(),
        random_seed: $('#cointoss_random_seed').val(),
        initial_setup_cost: $('#cointoss_initial_setup_cost').val(),
        facility_unit_cost: $('#cointoss_facility_unit_cost').val(),
        facility_unit: $('#cointoss_facility_unit').val(),
        save_each_transitions: $('#cointoss_save_each_transitions:checked').val()
    };

    $.ajax({
        type: 'POST',
        url: '/api/calculate/multi',
        data: data,
        format: 'json',
    }).done(function (data) {
        tokenId = data.token;
        startGettingProgress(tokenId, 'coin-tossing-progress-bar');
        setTimeout(checkProgressComplete, 500);
    }).fail(function (data) {
        setErrorMessage($.parseJSON(data.responseText));
        afterCalculateByError('coin_tossing_spinner');
        clearProgressBar();
    });
}

export function switchCustomForm(mode)
{
    if (mode === 'custom') {
        $('#render_custom_from').attr('disabled', false);
        $('#render_custom_to').attr('disabled', false);
    } else {
        $('#render_custom_from').attr('disabled', true);
        $('#render_custom_to').attr('disabled', true);
    }
}

function checkProgressComplete()
{
    let returned_data = getReturnedDataIfExist();
    if (!_.isNull(returned_data) && returned_data.status === 'failed') {
        return;
    } else if (!_.isNull(returned_data) && returned_data.status === 'complete') {
        afterMultiCalculateSuccess(returned_data.result);
        clearProgressBar();
    } else {
        setTimeout(checkProgressComplete, 500);
    }
}

function afterMultiCalculateSuccess(returned_data)
{
    $('#chart_area_single').hide();
    $('#chart_area_multi').show();
    $('#chart_area_multi_child').hide();

    initRerenderElements(returned_data.chart_data);
    setMultiDistributionResult(returned_data.chart_data);
    renderMultiDistributionResult();
    $('button.calculate').removeClass('disabled');
    $('#coin_tossing_spinner').hide();
    let formatter = new Intl.NumberFormat('ja-JP');
    $('#multi_report_average').html(formatter.format(returned_data.average));
    $('#multi_report_standard_deviation').html(formatter.format(returned_data.standard_deviation));
    $('#multi_report_iteration').html(formatter.format(returned_data.iteration));
    $('#multi_report_increasing_cases').html(formatter.format(returned_data.result_status.increase));
    // $('#multi_report_result_decrease').html(formatter.format(returned_data.result_status.decrease));
    $('#multi_report_bankruptcy_cases').html(formatter.format(returned_data.result_status.bankruptcy));
    // $('#multi_report_result_even').html(formatter.format(returned_data.result_status.even));
    $('#multi_report_cost').html(formatter.format(returned_data.cost));
    $('#multi_report_roi').html(formatter.format(returned_data.roi));
    $('#multi_save_each_transitions').val(returned_data.save_each_transitions);
    if (returned_data.save_each_transitions) {
        $('#render_multi_child').show();
    } else {
        $('#render_multi_child').hide();
    }
    $('#multi_step').val(returned_data.chart_header.step);
    $('#multi_min_cache').val(returned_data.chart_header.min_cache);
    $('#multi_max_cache').val(returned_data.chart_header.max_cache);

    let offset =  $('#chart_area_multi').offset().top - $('.kt-pagetitle').offset().top -20;
    $("html,body").animate({scrollTop: offset});
    $('.calculate_progress-bar').hide();
}

function setMultiDistributionResult(returned_data)
{
    multiDistributionData = getMultiDistributionData(returned_data);
}

function getMultiDistributionData(returned_data)
{
    return returned_data;
}

/**
 *複数回実行した時の、分布をレンダリングする
 */
function renderMultiDistributionResult()
{
    let distribution_data = getFilteredDistributionData();
    let ctx = document.getElementById('chart_multi');
    if(myChartMulti) {
        myChartMulti.destroy();
        $('#chart_multi').removeAttr('width');
    }
    myChartMulti = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: _.pluck(distribution_data, 'x'),
            datasets: [
                {
                    label: '# ヒット件数',
                    data: distribution_data,
                    borderColor: '#324463',
                    borderWidth: 2,
                    pointRadius: 0,
                    fill: false
                },
            ]
        },
        options: {
            responsive: true,
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
                        beginAtZero: false,
                        fontSize: 11,
                    },
                    scaleLabel: {
                        display: true,
                        fontSize: 15,
                        labelString: '胴元の収支金額'
                    }
                }]
            },
            tooltips: {
                mode: 'index',
                intersect: false
            },
            onClick: function (e, el) {
                let element = this.getElementsAtXAxis(e);
                if (element.length > 0) {
                    let index = element[0]['_index'];
                    let x_label = element[0]['_xScale']['ticks'][index];
                    $('.child_x_label').html(x_label);
                    $('#child_x_label').val(x_label);
                }
            }
        }
    });
}

function initRerenderElements(chart_data)
{
    $('#multi_render_mode_all').prop('checked', true);
    $('#render_custom_from').attr('disabled', true);
    $('#render_custom_to').attr('disabled', true);
    $('#render_custom_from').val(chart_data[0].x);
    $('#render_custom_to').val(chart_data[chart_data.length - 1].x);
}

function getFilteredDistributionData()
{
    let ret = [];
    let render_mode = $('input[name="multi_render_mode"]:checked').val();
    if (render_mode === 'bankruptcy') {
        $.each(multiDistributionData,function(index,val){
            if (val.x < 0) {
                ret.push(val);
            }
        });
        return ret;
    } else if (render_mode === 'custom') {
        let from = $('#render_custom_from').val();
        let to = $('#render_custom_to').val();
        $.each(multiDistributionData,function(index,val){
            if (val.x >= from && val.x <= to) {
                ret.push(val);
            }
        });
        return ret;
    }
    return multiDistributionData;
}

export function reRenderMultiCalculate()
{
    let render_mode = $('input[name="multi_render_mode"]:checked').val();
    if (render_mode === 'custom') {
        let from = $('#render_custom_from').val();
        let to = $('#render_custom_to').val();
        if (!$.isNumeric(from) || !$.isNumeric(to)) {
            $('#multi_re_render_alert_danger_message').html('範囲指定が不正です');
            $('#multi_re_render_alert_danger').show();
            return;
        }
        if (from > to) {
            $('#multi_re_render_alert_danger_message').html('範囲指定が不正です');
            $('#multi_re_render_alert_danger').show();
            return;
        }
    }
    $('#multi_re_render_alert_danger').hide();
    renderMultiDistributionResult();
}
