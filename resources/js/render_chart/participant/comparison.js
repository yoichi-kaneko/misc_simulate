import {afterCalculateByError, setErrorMessage} from "../../functions/calculate";
import {setNextFlagOn} from "../../functions/move_step";
import {notifyComplete} from "../../functions/notify";
import {clearProgressBar, getReturnedDataIfExist, startGettingProgress} from "../../functions/progress";

let myChartComparison;
let myChartParticipantsSimulation;
let tokenId;

export function doComparisonCalculate()
{
    let data = makePostParam();
    if ($('#distribution_number_total').val() != 100) {
        let offset =  $('#distribution_number_total_message').offset().top - $('.kt-pagetitle').offset().top -20;
        $("html,body").animate({scrollTop: offset});
        afterCalculateByError('participants_spinner');
        return false;
    }
    $.ajax({
        type: 'POST',
        url: '/api/linear/comparison/calculate',
        data: data,
        format: 'json',
        success: function (data) {
            $('#chart_area_participants_simulation').hide();
            renderComparisonArea(data.result);
            notifyComplete();
            setNextFlagOn();
        },
        error: function (data) {
            setErrorMessage($.parseJSON(data.responseText));
            afterCalculateByError('participants_spinner');
        }
    });
}

export function doNonLinearComparisonCalculate()
{
    let data = makeNonLinearPostParam();
    if ($('#distribution_number_total').val() != 100) {
        let offset =  $('#distribution_number_total_message').offset().top - $('.kt-pagetitle').offset().top -20;
        $("html,body").animate({scrollTop: offset});
        afterCalculateByError('participants_spinner');
        return false;
    }
    $.ajax({
        type: 'POST',
        url: '/api/non_linear/comparison/calculate',
        data: data,
        format: 'json',
        success: function (data) {
            $('#chart_area_participants_simulation').hide();
            renderNonLinearComparisonArea(data.result);
            bindExpandFormulaModal();
            notifyComplete();
            setNextFlagOn();
        },
        error: function (data) {
            setErrorMessage($.parseJSON(data.responseText));
            let offset =  $('#alert_danger').offset().top - $('.kt-pagetitle').offset().top -20;
            $("html,body").animate({scrollTop: offset});
            afterCalculateByError('participants_spinner');
        }
    });
}

export function doParticipantsSimulationCalculate()
{
    let data = makePostParam();
    data['participants_allocate_mode'] = $('#participants_allocate_mode').val();
    if ($('#distribution_number_total').val() != 100) {
        let offset =  $('#distribution_number_total_message').offset().top - $('.kt-pagetitle').offset().top -20;
        $("html,body").animate({scrollTop: offset});
        afterCalculateByError('participants_spinner');
        return false;
    }
    $.ajax({
        type: 'POST',
        url: '/api/participants_simulate/calculate',
        data: data,
        format: 'json',
        success: function (data) {
            tokenId = data.token;
            startGettingProgress(tokenId, 'participants-simulate-progress-bar');
            setTimeout(checkProgressComplete, 500);
        },
        error: function (data) {
            setErrorMessage($.parseJSON(data.responseText));
            afterCalculateByError('participants_distribution_spinner');
            clearProgressBar();
        }
    });
}

function makePostParam()
{
    let cognitive_degrees_distribution = {};

    $('.distribution_number').each(function(index, element){
        cognitive_degrees_distribution[$(element).attr('number')] = $(element).val();
    });

    let data = {
        prize_unit: $('#prize_unit').val(),
        bankers_budget: $('#bankers_budget').val(),
        participation_fee: $('#participation_fee').val(),
        potential_participants: $('#potential_participants').val(),
        cognitive_degrees_distribution: cognitive_degrees_distribution,
    };
    return data;
}

function makeNonLinearPostParam()
{
    let cognitive_degrees_distribution = {};

    $('.distribution_number').each(function(index, element){
        cognitive_degrees_distribution[$(element).attr('number')] = $(element).val();
    });

    let data = {
        prize_unit: $('#prize_unit').val(),
        bankers_budget: $('#bankers_budget').val(),
        participation_fee: $('#participation_fee').val(),
        potential_participants: $('#potential_participants').val(),
        cognitive_degrees_distribution: cognitive_degrees_distribution,
        theta_function_formula: $('#theta_function_formula').val(),
    };
    return data;
}

function checkProgressComplete()
{
    let returned_data = getReturnedDataIfExist();
    if (!_.isNull(returned_data) && returned_data.status === 'failed') {
        return;
    } else if (!_.isNull(returned_data) && returned_data.status === 'complete') {
        afterParticipantsSimulationSuccess(returned_data.result);
        clearProgressBar();
    } else {
        setTimeout(checkProgressComplete, 500);
    }
}

function renderComparisonArea(comparison_data)
{
    $('#chart_area_player').show();
    $('button.calculate').removeClass('disabled');
    $('#participants_spinner').hide();
    $('#expected_participant_number').val(comparison_data.expected_participant_number);
    renderResultTable(comparison_data.row, comparison_data);
    renderComparisonChart(comparison_data.row, comparison_data.cognitive_degrees_distribution);
}

function renderResultTable(row, result)
{
    let tmpl = $('#comparisonTemplate').render({
        row: row,
        participation_rate: result.participation_rate.toFixed(5),
        expected_participant_number: result.expected_participant_number.toLocaleString(),
        banker_budget_degree: result.banker_budget_degree,
        banker_maximum_prize: result.banker_maximum_prize,
        total_variance: result.total_variance.toFixed(2),
    });
    $('#comparison_result').html(tmpl);

    $('#comparison_result .katex_exp').each(function () {
        let element = $(this)[0];
        katex.render($(this).attr('expression'), element, {
            throwOnError: false
        });
    });
    $('#comparison_result [data-popover-color="default"]').popover();
    if ($('#showmore-button-comparison_result').length == 0) {
        $('#comparison_result').showMore({
            minheight: 300
        });
    }
}

function renderComparisonChart(row, cognitive_degrees_distribution)
{
    let transition_data = getComparisonTransitionData(row);
    let distribution_data = getCognitiveDegreesDistributionData(cognitive_degrees_distribution);

    let ctx = document.getElementById('chart_comparison');
    if(myChartComparison) {
        myChartComparison.destroy();
        $('#chart_comparison').removeAttr('width');
    }

    myChartComparison = new Chart(ctx, getComparisonChartOption(transition_data, distribution_data));
}

function renderNonLinearComparisonChart(row, cognitive_degrees_distribution)
{
    let transition_data = getComparisonTransitionData(row);
    let distribution_data = getCognitiveDegreesDistributionData(cognitive_degrees_distribution);

    let ctx = document.getElementById('chart_comparison');
    if(myChartComparison) {
        myChartComparison.destroy();
        $('#chart_comparison').removeAttr('width');
    }

    myChartComparison = new Chart(ctx, getComparisonChartOption(transition_data, distribution_data));
}

function getComparisonTransitionData(result)
{
    let transition_data = [];

    $.each(result,function(index,val) {
        transition_data.push(
            {
                label: val.cognitive_degree,
                data: {
                    x: val.cognitive_degree,
                    y: val.comparison.value,
                    displayText: val.comparison.display
                },
            }
        );
    });
    return transition_data;
}

function getCognitiveDegreesDistributionData(cognitive_degrees_distribution)
{
    let distribution_data = [];
    let display_text = '';
    $.each(cognitive_degrees_distribution,function(index,val) {
        display_text = (val > 0) ? val + '/100' : val;

        distribution_data.push(
            {
                label: index,
                data: {
                    x: index,
                    y: val,
                    displayText: display_text
                },
            }
        );
    });
    return distribution_data;
}

function getComparisonChartOption(transition_data, distribution_data) {
    let transition_label_array = _.pluck(transition_data, 'label');
    let transition_data_array = _.pluck(transition_data, 'data');
    let distribution_data_array = _.pluck(distribution_data, 'data');

    return {
        data: {
            labels: transition_label_array,
            datasets: [
                {
                    type: 'line',
                    yAxisID: 'y-axis-1',
                    label: '# Behavioral Probability of Choice A',
                    data: transition_data_array,
                    borderColor: '#324463',
                    borderWidth: 2,
                    lineTension: 0,
                    pointRadius: 0,
                    fill: false
                },
                {
                    type: 'bar',
                    yAxisID: 'y-axis-2',
                    label: '# Distributions',
                    data: distribution_data_array,
                    backgroundColor: '#218bc2'
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
                        max: 1,
                        fontSize: 10,
                    }
                }, {
                    id: "y-axis-2",
                    type: "linear",
                    position: "right",
                    ticks: {
                        beginAtZero: true,
                        min: 0,
                        max: 100,
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
                        labelString: 'cognitive degree'
                    }
                }]
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(tooltipItem, data) {
                        let label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label) {
                            label += ': ';
                        }
                        label += data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].displayText;
                        return label;
                    }
                }
            },
        }
    }
}

function renderNonLinearComparisonArea(comparison_data)
{
    $('#chart_area_player').show();
    $('button.calculate').removeClass('disabled');
    $('#participants_spinner').hide();
    $('#expected_participant_number').val(comparison_data.expected_participant_number);
    renderNonLinearResultTable(comparison_data.row, comparison_data);
    renderNonLinearComparisonChart(comparison_data.row, comparison_data.cognitive_degrees_distribution);　
}

function renderNonLinearResultTable(row, result)
{
    let tmpl = $('#comparisonTemplate').render({
        row: row,
        banker_budget_degree: result.banker_budget_degree,
        banker_maximum_prize: result.banker_maximum_prize,
        theta_function_values: result.theta_function_values,
        participation_rate: result.participation_rate.toFixed(5),
        expected_participant_number: result.expected_participant_number.toLocaleString(),
        total_variance: result.total_variance.toFixed(2),
    });
    $('#comparison_result').html(tmpl);

    $('#comparison_result .katex_exp').each(function () {
        let element = $(this)[0];
        katex.render($(this).attr('expression'), element, {
            throwOnError: false
        });
    });
    $('#comparison_result [data-popover-color="default"]').popover();
    if ($('#showmore-button-comparison_result').length == 0) {
        $('#comparison_result').showMore({
            minheight: 300
        });
    }
}

function bindExpandFormulaModal()
{
    $('.expanded_formula').click(function (){
        let element = $('#expanded_formula_modal_text')[0];
        katex.render($(this).data('expanded_formula_expression'), element, {
            throwOnError: false
        });
        $('#expanded_formula_modal').modal();
    });
}

function afterParticipantsSimulationSuccess(returned_data)
{
    renderComparisonArea(returned_data.comparison_result);

    renderParticipantsSimulationResultTable(
        returned_data.participants_result.iteration,
        returned_data.participants_result.potential_participants,
        returned_data.participants_result.expected_participants,
        returned_data.participants_result.total_variance,
        returned_data.participants_result.total_variance_root,
        returned_data.participants_result.first_confidence_interval,
        returned_data.participants_result.second_confidence_interval
    );
    $('#participants_simulation_result [data-popover-color="default"]').popover();
    $('#chart_area_participants_simulation').show();
    renderParticipantsSimulationChart(
        returned_data.participants_result.chart_header,
        returned_data.participants_result.chart_data
    );
    $('button.calculate').removeClass('disabled');
    $('#participants_distribution_spinner').hide();
    notifyComplete();
    setNextFlagOn();
}

function renderParticipantsSimulationResultTable(
    iteration,
    potential_participants,
    expected_participants,
    total_variance,
    total_variance_root,
    first_confidence_interval,
    second_confidence_interval
) {
    let tmpl = $('#participantsSimulationResultTemplate').render({
        iteration: iteration,
        potential_participants: potential_participants,
        expected_participants: expected_participants,
        total_variance: total_variance,
        total_variance_root: total_variance_root,
        first_confidence_interval: first_confidence_interval,
        second_confidence_interval: second_confidence_interval
    });
    $('#participants_simulation_result').html(tmpl);

    $('#participants_simulation_result .katex_exp').each(function () {
        let element = $(this)[0];
        katex.render($(this).attr('expression'), element, {
            throwOnError: false
        });
    });
}

function renderParticipantsSimulationChart(chart_header, chart_data)
{
    let ctx_simulation = document.getElementById('chart_participants_simulation');
    if(myChartParticipantsSimulation) {
        myChartParticipantsSimulation.destroy();
        $('#chart_participants_simulation').removeAttr('width');
    }

    myChartParticipantsSimulation = new Chart(ctx_simulation, getParticipantsSimulationOption(chart_header, chart_data));
}

function getParticipantsSimulationOption(chart_header, chart_data)
{
    let render_data = [];
    $.each(chart_data, function(index,val) {
        render_data.push(
            {
                label: val.x,
                data: {
                    x: val.x,
                    y: val.y,
                    displayLabel: val.x_from + '-' + val.x_to,
                    displayText: val.y
                },
            }
        );
    });
    let label_array = _.pluck(render_data, 'label');
    let data_array = _.pluck(render_data, 'data');

    return {
        data: {
            labels: label_array,
            datasets: [
                {
                    type: 'line',
                    yAxisID: 'y-axis-1',
                    label: 'x-axis-#Participants / y-axis-#runs',
                    data: data_array,
                    borderColor: '#324463',
                    borderWidth: 2,
                    lineTension: 0.5,
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
                        labelString: '#runs'
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
                        labelString: '#Participants'
                    }
                }]
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(tooltipItem, data) {
                        let label = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].displayLabel || '';

                        if (label) {
                            label += ': ';
                        }
                        label += data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].displayText;
                        return label;
                    }
                }
            },
        }
    }
}

