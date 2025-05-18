const {beforeCalculate} = require("../functions/calculate");
const {doCentipedeCalculate} = require("../functions/centipede");

// let parser = UAParser();

$(function(){
    $('.simulate_player button.calculate').click(function () {
        if (!$(this).hasClass('disabled')) {
            beforeCalculate('nash_spinner');
            // doCentipedeCalculate();
        }
    });

    $('button#reset').click(function () {
        reset();
    });

    $('.form-layout .katex_exp').each(function () {
        let element = $(this)[0];
        katex.render($(this).attr('expression'), element, {
            throwOnError: false
        });
    });
});

function reset()
{
    $('#nash_block input.form-control').each(function () {
        if (!$(this).attr('readonly')) {
            $(this).val($(this).attr('default_val'));
        }
    });
}
