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
        togglePatternB();
    });
    $('input#simulate_union_mode').click(function () {
        togglePatternB();
    });

    $('.form-layout .katex_exp').each(function () {
        let element = $(this)[0];
        katex.render($(this).attr('expression'), element, {
            throwOnError: false
        });
    });
    $('#cognitive_unit_formula .katex').css('font-size', '1.3rem');
    $('input.pattern_b').prop('disabled', true);
    $('input.union_player').prop('disabled', true);

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

function togglePatternB()
{
    let is_enable = $('input#enable_pattern_b').prop('checked');
    if (is_enable) {
        $('input.pattern_b').prop('disabled', false);
    } else {
        $('input.pattern_b').prop('disabled', true);
    }
    if (is_enable && $('input#simulate_union_mode').prop('checked')) {
        $('input.union_player').prop('disabled', false);
    } else {
        $('input.union_player').prop('disabled', true);
    }
}
