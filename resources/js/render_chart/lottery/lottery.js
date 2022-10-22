import {afterCalculateByError, setErrorMessage} from "../../functions/calculate";

export function doLotteryCalculate()
{
    let data = {
        lottery_rates: getLotteryRates(),
    };

    $.ajax({
        type: 'POST',
        url: '/api/lottery/calculate',
        data: data,
        format: 'json',
        success: function (data) {
            $('#lottery_area').show();
            afterCalculateBySuccess(data);
        },
        error: function (data) {
            setErrorMessage($.parseJSON(data.responseText));
            afterCalculateByError('lottery_spinner');
            $('#chart_area_lottery').hide();
        }
    });
}

function getLotteryRates()
{
    let data = [];
    $('.rate_block').each(function (){
        data.push(
            {
                prize: $(this).find('.prize:first').val(),
                rate_number: $(this).find('.rate_number:first').val(),
                rate_digit: $(this).find('.rate_digit:first').val(),
            }
        );
    });
    return data;
}

function afterCalculateBySuccess(returned_data) {
    let tmpl = $('#lotteryResultTemplate').render({
        result: returned_data.result,
    });
    $('#lottery_result').html(tmpl);
    $('button.calculate').removeClass('disabled');
    $('#lottery_spinner').hide();
    $('#chart_area_lottery').show();
}
