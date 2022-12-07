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
});

function reset()
{
    $('#centipede_block input.form-control').each(function () {
        if (!$(this).attr('readonly')) {
            $(this).val($(this).attr('default_val'));
        }
    });
}