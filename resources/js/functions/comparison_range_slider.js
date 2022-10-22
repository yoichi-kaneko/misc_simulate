import RangeSlider from "rangeslider-js";

let sliderEl = {};

export function createRangeSlider()
{
    let iteration = parseInt($('#max_banker_budget_degree').val());

    if (iteration <= 0) {
        return;
    }
    if (!$('#player_distributions').length) {
        return;
    }
    $('.distribution_block').remove();
    $('#distribution_number_total').val(0);

    let tmpl;
    sliderEl = {};
    let numberEl = {};
    let targetNumberEl;
    let i;

    for (i = 0; i <= iteration; i++) {
        tmpl = $('#distributionTemplate').render({i: i});
        $('#player_distributions').append(tmpl);
        sliderEl[i] = document.querySelector('#distribution_slider_' + i);
        numberEl[i] = $('#distribution_number_' + i);
        targetNumberEl = numberEl[i];
        RangeSlider.create(sliderEl[i], {
            onSlide: (value, percent, position) => {
                updateRangeSliderValues();
                updateRangeTotalNumber();
            }
        });
        numberEl[i].change(function() {
            let number = $(this).attr('number');
            sliderEl[number]['rangeslider-js'].update({value: $(this).val()});
            updateRangeTotalNumber();
        });
    }

    setDefaultDistribution();
}

export function updateRangeSlider(number, new_value)
{
    sliderEl[number]['rangeslider-js'].update({value: new_value});
}

export function refreshRangeSlider()
{
    updateRangeSliderValues();
    updateRangeTotalNumber();
}

function updateRangeSliderValues()
{
    let iteration = parseInt($('#max_banker_budget_degree').val());

    if (iteration <= 0) {
        return false;
    }

    let i;
    for (i = 0; i <= iteration; i++) {
        $('#distribution_number_' + i).val(sliderEl[i]['rangeslider-js']['value']);
    }
}

function updateRangeTotalNumber() {
    let total = 0;

    $('.distribution_number').each(function(index, element){
        total += parseInt($(element).val());
    });
    $('#distribution_number_total').val(total);

    if (total == 100) {
        $('#distribution_number_total_message').hide();
    } else {
        $('#distribution_number_total_message').show();
    }
}

export function setDefaultDistribution()
{
    let distribution_params = getDistributionParams();
    let distribution_peak = parseInt($('#distribution_peak').val());
    let key;
    let number;
    $('.distribution_number').each(function(index, element){
        $(element).val(0);
        number = $(element).attr('number');
        sliderEl[number]['rangeslider-js'].update({value: 0});
    });
    $.each(distribution_params, function(index,val){
        key = distribution_peak + val.offset;
        if ($('#distribution_number_' + key).length > 0) {
            $('#distribution_number_' + key).val(val.number);
            sliderEl[key]['rangeslider-js'].update({value: val.number});
        }
    });

    updateRangeTotalNumber();
}

function getDistributionParams() {
    let ret = [];

    ret.push({
        offset: -3,
        number: 5
    });
    ret.push({
        offset: -2,
        number: 10
    });
    ret.push({
        offset: -1,
        number: 20
    });
    ret.push({
        offset: 0,
        number: 30
    });
    ret.push({
        offset: 1,
        number: 20
    });
    ret.push({
        offset: 2,
        number: 10
    });
    ret.push({
        offset: 3,
        number: 5
    });

    return ret;
}
