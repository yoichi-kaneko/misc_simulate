import {afterCalculateByError, setErrorMessage} from "./calculate";
import {notifyComplete} from "./notify";

export function doCentipedeCalculate()
{
    let data = {
        base_numerator: $('#base_numerator').val(),
        numerator_exp_1: $('#numerator_exp_1').val(),
        numerator_exp_2: $('#numerator_exp_2').val(),
        denominator_exp: $('#denominator_exp').val()
    };
    $.ajax({
        type: 'POST',
        url: '/api/centipede/calculate',
        data: data,
        format: 'json',
        success: function (data) {
            renderCentipedeReportArea(data.data);
            $('#cognitive_unit_value').html(data.cognitive_unit_value);

            let element = $('#cognitive_unit_latex_text');
            katex.render(data.cognitive_unit_latex_text, element[0], {
                throwOnError: false
            });

            $('button.calculate').removeClass('disabled');
            $('#centipede_spinner').hide();
            notifyComplete();
        },
        error: function (data) {
            setErrorMessage($.parseJSON(data.responseText));
            let offset =  $('#alert_danger').offset().top - $('.kt-pagetitle').offset().top -20;
            $("html,body").animate({scrollTop: offset});
            afterCalculateByError('centipede_spinner');
        }
    });
}

function renderCentipedeReportArea(data)
{
    let tmpl = $('#centipedeTemplate').render({
        data: data,
    });
    $('#centipede_result').html(tmpl);
    $('#centipede_result .katex_exp').each(function () {
        let element = $(this)[0];
        katex.render($(this).attr('expression'), element, {
            throwOnError: false
        });
    });
    $('#chart_area_centipede').show();
    if ($('#showmore-centipede_result').length == 0) {
                $('#centipede_result').showMore({
            minheight: 300
        });
    }
}