const DEFAULT_ELEMENT_NUMBER = 5;
const MAX_ELEMENT_NUMBER = 15;

export function initLotteryRate()
{
    let i;
    for (i = 0; i < DEFAULT_ELEMENT_NUMBER; i++){
        addLotteryRateElement();
    }
}

export function addLotteryRateElement()
{
    let cnt = $('.rate_block').length;
    if (cnt >= MAX_ELEMENT_NUMBER)
    {
        return;
    }
    let tmpl = $('#lotteryRateTemplate').render({count: (cnt + 1)});
    $('#lottery_rate_blocks').append(tmpl);
    bindLastElement();
}

function bindLastElement()
{
    let cnt = $('.rate_number:last').attr('count');
    calcRateLotteryBlock(cnt);
    $('#rate_number_' + cnt).change(function (){
        calcRateLotteryBlock($(this).attr('count'));
    });
    $('#rate_digit_' + cnt).change(function (){
        calcRateLotteryBlock($(this).attr('count'));
    });
}

function calcRateLotteryBlock(block_number)
{
    let number = $('#rate_number_' + block_number).val();
    let digit = $('#rate_digit_' + block_number).val();
    $('#display_amount_' + block_number).html(exchangeDisplayValue(number, digit));
}

function exchangeDisplayValue(number, digit)
{
    let string_num = String(number);
    let length = string_num.length;
    if (length <= digit) {
        string_num = string_num.padStart(digit, '0');
        return '0.' + string_num;
    } else {
        number = number * Math.pow(10, digit * -1);
        return number.toFixed(digit);
    }
}

export function removeLastLotteryRateElement()
{
    let cnt = $('.rate_block').length;
    if (cnt == 0)
    {
        return;
    }
    $('#block_' + cnt).remove();
}

export function resetLotteryRateElement()
{
    $('#lottery_rate_blocks').empty();
    initLotteryRate();
}
