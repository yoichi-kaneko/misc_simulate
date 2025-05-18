import {afterCalculateByError, setErrorMessage} from "./calculate";
import {notifyComplete} from "./notify";

export function doNashCalculate()
{
    const data = {
        alpha_1: {
            numerator: $('#alpha_1_numerator').val(),
            denominator: $('#alpha_1_denominator').val(),
        },
        alpha_2: {
            numerator: $('#alpha_2_numerator').val(),
            denominator: $('#alpha_2_denominator').val(),
        },
        beta_1: {
            numerator: $('#beta_1_numerator').val(),
            denominator: $('#beta_1_denominator').val(),
        },
        beta_2: {
            numerator: $('#beta_2_numerator').val(),
            denominator: $('#beta_2_denominator').val(),
        },
    };
    $.ajax({
        type: 'POST',
        url: '/api/nash/calculate',
        data: data,
        success: function (data) {
            $('button.calculate').removeClass('disabled');
            $('#nash_spinner').hide();
            notifyComplete();
        },
        error: function (data) {
            setErrorMessage($.parseJSON(data.responseText));
            let offset =  $('#alert_danger').offset().top - $('.kt-pagetitle').offset().top -20;
            $("html,body").animate({scrollTop: offset});
            afterCalculateByError('nash_spinner');
        }
    });
}