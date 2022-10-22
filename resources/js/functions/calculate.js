
export function beforeCalculate(spinner_id) {
    $('button.calculate').addClass('disabled');
    $('#' + spinner_id).show();
    $('.alert_block').hide();
}

export function afterCalculateByError(spinner_id) {
    $('button.calculate').removeClass('disabled');
    $('#' + spinner_id).hide();
    $('#chart_area_single').hide();
    $('#chart_area_multi').hide();
}

export function setErrorMessage(data) {
    let key_list = Object.keys(data.errors);
    $('#alert_danger_message').html(data.errors[key_list[0]].shift());
    $('#alert_danger').show();
}
