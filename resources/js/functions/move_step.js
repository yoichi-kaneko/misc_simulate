import {getExpVal} from "./exponential";

export function goNext()
{
    if ($('#go_next').hasClass('disabled')) {
        return;
    }
    $('#cointoss_participant_number').val($('#expected_participant_number').val());
    $('#cointoss_participant_number').attr('readonly', true);
    $('#cointoss_participation_fee').val($('#participation_fee').val());
    $('#cointoss_participation_fee').attr('readonly', true);
    $('#cointoss_banker_budget_degree').val(getExpVal($('#bankers_budget').val()));
    $('#cointoss_banker_budget_degree').attr('readonly', true);
    $('#chart_area_player').fadeOut(300);
    $('#chart_area_participants_simulation').fadeOut(300);
    $('#participants_block').fadeOut(300, function (){
        $('#coin_tossing_block').fadeIn(300);
    });
}

export function setNextFlagOn()
{
    $('#go_next').removeClass('disabled');
}

export function setNextFlagOff()
{
    $('#go_next').addClass('disabled');
}

export function goPrev()
{
    $('#chart_area_single, #chart_area_multi, #chart_area_multi_child').fadeOut(300);
    $('#coin_tossing_block').fadeOut(300, function (){
        $('#participants_block, #chart_area_player').fadeIn(300);
    });
}
