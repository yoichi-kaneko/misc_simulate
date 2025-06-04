import { beforeCalculate } from "../functions/calculate";
import { doNashCalculate } from "../functions/nash";
import katex from "katex";

// let parser = UAParser();

$(function(){
    $('.simulate_player button.calculate').click(function () {
        if (!$(this).hasClass('disabled')) {
            beforeCalculate('nash_spinner');
            doNashCalculate();
        }
    });

    $('button#reset').click(function () {
        reset();
    });

    $('.form-layout .katex_exp').each(function () {
        let element = $(this)[0];
        katex.render($(this).attr('expression') || '', element, {
            throwOnError: false
        });
    });
});

function reset(): void {
    $('#nash_block input.form-control').each(function () {
        if (!$(this).attr('readonly')) {
            $(this).val($(this).attr('default_val'));
        }
    });
}