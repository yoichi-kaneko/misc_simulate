const {beforeCalculate} = require("../functions/calculate");
const {doCentipedeCalculate} = require("../functions/centipede");

let parser = UAParser();

$(function(){
    $('.simulate_player button.calculate').click(function () {
        if (!$(this).hasClass('disabled')) {
            beforeCalculate('centipede_spinner');
            doCentipedeCalculate();
        }
    });

    $('button#reset').click(function () {
        reset();
    });
    $('input#enable_pattern_b').click(function () {
        toggleRenderPattern();
    });
    $('input#simulate_union_mode').click(function () {
        toggleRenderPattern();
    });
    toggleRenderPattern();

    $('.form-layout .katex_exp').each(function () {
        let element = $(this)[0];
        katex.render($(this).attr('expression'), element, {
            throwOnError: false
        });
    });
    $('#cognitive_unit_formula .katex').css('font-size', '1.3rem');
    $('input.pattern_b').prop('disabled', true);
    $('input.union_player_1').prop('disabled', true);

    $('button.chart_download').click(function () {
        if (parser.browser.name == 'IE') {
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
});

function reset()
{
    $('#centipede_block input.form-control').each(function () {
        if (!$(this).attr('readonly')) {
            $(this).val($(this).attr('default_val'));
        }
    });
}

function toggleRenderPattern()
{
    if ($('input#simulate_union_mode').prop('checked')) {
        $('input.pattern_a_2').prop('disabled', false);
        $('input.union_player_1_a').prop('disabled', false);
    } else {
        $('input.pattern_a_2').prop('disabled', true);
        $('input.union_player_1_a').prop('disabled', true);
    }
    if ($('input#enable_pattern_b').prop('checked')) {
        $('input.pattern_b_1').prop('disabled', false);
    } else {
        $('input.pattern_b_1').prop('disabled', true);
    }
    if ($('input#simulate_union_mode').prop('checked') && $('input#enable_pattern_b').prop('checked')) {
        $('input.pattern_b_2').prop('disabled', false);
        $('input.union_player_1_b').prop('disabled', false);
    } else {
        $('input.pattern_b_2').prop('disabled', true);
        $('input.union_player_1_b').prop('disabled', true);
    }
}
