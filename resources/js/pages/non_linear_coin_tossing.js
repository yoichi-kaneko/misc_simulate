import {beforeCalculate, setErrorMessage} from "../functions/calculate";
import {doNonLinearComparisonCalculate} from "../render_chart/participant/comparison";
import {
    createRangeSlider,
    refreshRangeSlider,
    setDefaultDistribution,
    updateRangeSlider
} from "../functions/comparison_range_slider";
import {exchangeToExp} from "../functions/exponential";

createRangeSlider();

// プリセット種別。非線形は2が固定
const PRESET_TYPE = 2;

$(function(){
    $('button#reset_participants').click(function () {
        resetParticipantsData();
    });

    $('button#save_preset').click(function () {
        openPresetModal();
    });

    $('.simulate_player button.calculate').click(function () {
        if (!$(this).hasClass('disabled')) {
            beforeCalculate('participants_spinner');
            doNonLinearComparisonCalculate();
        }
    });

    $('#distribution_peak').change(function () {
        setDefaultDistribution();
    });

    $('#run_save_preset').click(function () {
        runSavePreset();
    });

    $('#load_preset').click(function () {
        loadPreset();
    });

    $('#delete_preset').click(function () {
        deletePreset();
    });

    $('#cointoss_preset').change(function () {
        changePreset($(this));
    });
    changePreset($('#cointoss_preset'));
});

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

/**
 * プリセットのモーダルオープンに関する処理
 */
function openPresetModal()
{
    $('#preset_modal').modal();
}

/**
 * プリセット保存処理を実行する
 */
function runSavePreset()
{
    // パラメータの取得
    let params = makeNonLinearPostParam();
    let data = {
        type: PRESET_TYPE,
        title: $('#preset_title').val(),
        params: params
    }
    let presetId = $('#cointoss_preset').val();
    if (presetId > 0 && $('#save_overwrite').prop('checked')) {
        data['id'] = presetId;
    }

    $('#preset_modal').modal('hide');

    $.ajax({
        type: 'POST',
        url: '/api/preset/save',
        data: data,
        format: 'json',
        success: function (data) {
            refreshPresetList(data.preset_list);
        },
        error: function (data) {
            setErrorMessage($.parseJSON(data.responseText));
            let offset =  $('#alert_danger').offset().top - $('.kt-pagetitle').offset().top -20;
            $("html,body").animate({scrollTop: offset});
        }
    });
}

/**
 * パラメータを取得する
 * @returns {{potential_participants: (*|string|jQuery), cognitive_degrees_distribution: {}, theta_function_formula: (*|string|jQuery), bankers_budget: (*|string|jQuery), prize_unit: (*|string|jQuery), participation_fee: (*|string|jQuery)}}
 */
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

/**
 * プリセットデータを取得する
 */
function loadPreset()
{
    let presetId = $('#cointoss_preset').val();
    if (presetId == 0) {
        return;
    }
    $.ajax({
        type: 'GET',
        url: '/api/preset/find/' + presetId,
    }).done(function (data) {
        setLoadedParams(data.params);
    });
}

/**
 * プリセットデータを削除する
 */
function deletePreset()
{
    let presetId = $('#cointoss_preset').val();
    if (presetId == 0) {
        return;
    }
    let data = {
        id: presetId,
        type: PRESET_TYPE
    };
    $('#preset_modal').modal('hide');
    $.ajax({
        type: 'POST',
        url: '/api/preset/delete',
        data: data,
    }).done(function (data) {
        refreshPresetList(data.preset_list);
    });
}

function setLoadedParams(params)
{
    $('#prize_unit').val(params.prize_unit);
    $('#bankers_budget').val(params.bankers_budget);
    $('#participation_fee').val(params.participation_fee);
    $('#potential_participants').val(params.potential_participants);
    $('#theta_function_formula').val(params.theta_function_formula);

    let cognitive_degrees_distribution = 0;
    $('.distribution_number').each(function(element){
        let index = $(this).attr('number');
        if(index in params.cognitive_degrees_distribution) {
            cognitive_degrees_distribution = params.cognitive_degrees_distribution[index];
        } else {
            cognitive_degrees_distribution = 0;
        }
        updateRangeSlider(index, cognitive_degrees_distribution);
    });
    refreshRangeSlider();
}

/**
 * プリセット一覧を更新する
 * @param preset_list
 */
function refreshPresetList(preset_list)
{
    let option_string = '';
    $.each(preset_list, function(index, val) {
        let escapeHTML = val
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        option_string += '<option value="' + index + '">' + escapeHTML + '</option>';
    });

    $('#cointoss_preset').html(option_string);
    $('#cointoss_preset').select2({
        minimumResultsForSearch: Infinity
    });
}

function changePreset(elm)
{
    // 0の場合にはLoadを無効化するなどの処理を行う。
    if ($(elm).val() == 0) {
        $('#load_preset').prop('disabled', true);
        $('#preset_title').val('');
        $('#delete_preset').hide();
        $('#save_overwrite_block').hide();
        $('#save_overwrite').prop('checked', false);
    } else {
        $('#load_preset').prop('disabled', false);
        let title = $(elm).find('option:selected').text();
        $('#preset_title').val(title);
        $('#delete_preset').show();
        $('#save_overwrite_block').show();
        $('#save_overwrite').prop('checked', true);
    }
}
