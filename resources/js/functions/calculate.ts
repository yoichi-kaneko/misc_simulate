
/**
 * Functions related to calculation process UI handling
 */

/**
 * Prepare UI for calculation process
 * @param {string} spinner_id - The ID of the spinner element to show
 */
export function beforeCalculate(spinner_id: string): void {
  $('button.calculate').addClass('disabled');
  $('#' + spinner_id).show();
  $('.alert_block').hide();
}

/**
 * Reset UI after calculation error
 * @param {string} spinner_id - The ID of the spinner element to hide
 */
export function afterCalculateByError(spinner_id: string): void {
  $('button.calculate').removeClass('disabled');
  $('#' + spinner_id).hide();
  $('#chart_area_single').hide();
  $('#chart_area_multi').hide();
}

/**
 * Display error message from API response
 * @param {object} data - The error data from API response
 */
export function setErrorMessage(data: { errors: Record<string, string[]> }): void {
  const key_list = Object.keys(data.errors);
  $('#alert_danger_message').html(data.errors[key_list[0]].shift() || '');
  $('#alert_danger').show();
}