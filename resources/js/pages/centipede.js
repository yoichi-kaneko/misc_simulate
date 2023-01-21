const {beforeCalculate} = require("../functions/calculate");
const {doCentipedeCalculate} = require("../functions/centipede");

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

    $('.form-layout .katex_exp').each(function () {
        let element = $(this)[0];
        katex.render($(this).attr('expression'), element, {
            throwOnError: false
        });
    });
    $('#cognitive_unit_formula .katex').css('font-size', '1.3rem');
});

function reset()
{
    $('#centipede_block input.form-control').each(function () {
        if (!$(this).attr('readonly')) {
            $(this).val($(this).attr('default_val'));
        }
    });
}