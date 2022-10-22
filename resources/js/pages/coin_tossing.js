import {beforeCalculate} from "../functions/calculate";
import {animateChart, doSingleCalculate} from "../render_chart/coin_tossing/single";
import {doMultiCalculate, reRenderMultiCalculate, switchCustomForm} from "../render_chart/coin_tossing/multi";
import {doMultiChildCalculate} from "../render_chart/coin_tossing/multi_child";
import {doComparisonCalculate, doParticipantsSimulationCalculate} from "../render_chart/participant/comparison";
import {createRangeSlider, setDefaultDistribution} from "../functions/comparison_range_slider";
import {exchangeToExp} from "../functions/exponential";
import {goNext, goPrev, setNextFlagOff} from "../functions/move_step";

let parser = UAParser();

createRangeSlider();
$('.simulate_main button.calculate').click(function () {
    if (!$(this).hasClass('disabled')) {
        beforeCalculate('coin_tossing_spinner');
        runCoinTossingCalculate();
    }
});

$('button#reset_cointossing').click(function () {
    resetCoinTossingData();
});

$('button#reset_participants').click(function () {
    resetParticipantsData();
});

$('button.chart_animate').click(function () {
    let target_id = $(this).attr('target');
    animateChart(target_id);
});

toggleCalculateModeField();
$('#cointoss_calculate_mode').change(function () {
    toggleCalculateModeField();
});

toggleParticipantsSimulateModeField();
$('#run_participants_simulation').change(function () {
    toggleParticipantsSimulateModeField();
});

$('#go_next').click(function () {
    goNext();
});

$('#go_prev').click(function () {
    goPrev();
});

$('#participants_block input').change(function () {
    setNextFlagOff();
});

$('button.chart_download').click(function () {
    if (parser.browser.name == 'IE' || parser.browser.name == 'Edge') {
        alert('ダウンロード機能はIEでは利用できません。他のブラウザで再度確認してください。');
        return;
    }
    let target = '#' + $(this).attr('target');
    let canvas = $(target)[0];

    let tmpl = $('#downloadTemplate').render({href: canvas.toDataURL('image/png')});
    $('body').append(tmpl);
    $('#image-file')[0].click();
    $('#image-file').remove();
});

function runCoinTossingCalculate() {
    let mode = $('#cointoss_calculate_mode').val();
    if (mode == 'multi') {
        doMultiCalculate();
    } else {
        doSingleCalculate();
    }
}

function resetCoinTossingData()
{
    $('#coin_tossing_block input.form-control').each(function () {
        if (!$(this).attr('readonly')) {
            $(this).val($(this).attr('default_val'));
        }
    });
}

function resetParticipantsData(){
    $('#participants_block input.form-control').each(function () {
        if (!$(this).attr('readonly')) {
            $(this).val($(this).attr('default_val'));
        }
    });
    $('.form-exponential-value').each(function(element){
        let name = $(this).data('name');
        exchangeToExp(name);
        $('.check_exp[data-name="' + name + '"]').prop('checked', 'checked');
    });
    $('#distribution_peak').val($('#distribution_peak').attr('default_val'));
    createRangeSlider();
}

$('#multi_re_render').click(function () {
    reRenderMultiCalculate();
});

$('#render_multi_child').click(function () {
    doMultiChildCalculate();
});

$('.simulate_player button.calculate').click(function () {
    if (!$(this).hasClass('disabled')) {
        beforeCalculate('participants_spinner');
        if (!$('#run_participants_simulation').prop('checked')) {
            doComparisonCalculate();
        } else {
            doParticipantsSimulationCalculate();
        }
    }
});

$('input[name="multi_render_mode"]').click(function () {
    switchCustomForm($(this).val());
});

$('#distribution_peak').change(function () {
    setDefaultDistribution();
});

function toggleCalculateModeField() {
    let mode = $('#cointoss_calculate_mode').val();

    if (mode == 'multi') {
        $('.for_multi').show();
    } else {
        $('.for_multi').hide();
    }
}

function toggleParticipantsSimulateModeField() {
    let mode = $('#run_participants_simulation').prop('checked');

    if (mode) {
        $('#participants_allocate_mode_block').show();
    } else {
        $('#participants_allocate_mode_block').hide();
    }
}
