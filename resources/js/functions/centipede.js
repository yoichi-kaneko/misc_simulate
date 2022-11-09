import {afterCalculateByError, setErrorMessage} from "./calculate";
import {notifyComplete} from "./notify";

export function doCentipedeCalculate()
{
    let data = {
        case: $('#case').val()
    };
    $.ajax({
        type: 'POST',
        url: '/api/centipede/calculate',
        data: data,
        format: 'json',
        success: function (data) {
            renderCentipedeReportArea(data.data);
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
    if ($('#showmore-button-comparison_result').length == 0) {
        $('#centipede_result').showMore({
            minheight: 300
        });
    }
}