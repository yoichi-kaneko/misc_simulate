import {afterCalculateByError, setErrorMessage} from "./calculate";

let tokenId;
let start_time;
let progressBarId;
let returned_data;
const ready_exceed_time = 10;

function initProgressBar() {
    let progress_bar = $('#' + progressBarId +' .progress-bar');
    progress_bar.html('');
    progress_bar.css('width', 0);
    $('.calculate_progress-bar').show();
}

export function startGettingProgress(token, id) {
    tokenId = token;
    progressBarId = id;
    start_time =  new Date();
    returned_data = {};
    initProgressBar();
    setTimeout(getProgress, 1000);
}

function getProgress() {
    let progress_bar = $('#' + progressBarId +' .progress-bar');
    let is_failed = false;
    $.ajax({
        type: 'GET',
        url: '/api/calculate/progress/' + tokenId,
    }).done(function (data) {
        returned_data = data;
        if (data.status == 'ready') {
            progress_bar.html('');
            progress_bar.css('width', '0%');
            is_failed = isReadyTimeExceed();
            if (!is_failed) {
                setTimeout(getProgress, 1000);
            }
            // 100 未満の時は進捗レートを更新するのみ
        } else if (data.status == 'running') {
            progress_bar.html(displayText(data));
            progress_bar.css('width', data.progress_rate + '%');
            setTimeout(getProgress, 1000);
            // 100の場合に完了とする
        } else if(data.status == 'complete') {
            // 完了後の処理は呼び出し元で実装する
        } else if(data.status == 'failed') {
            is_failed = true;
        }
    }).fail(function (data) {
        setErrorMessage($.parseJSON(data.responseText));
        afterCalculateByError('coin_tossing_spinner');
        clearProgressBar();
    }).always(function (data){
        if (is_failed) {
            showErrorMessage('予期しないエラーが発生しました。システム管理者にお問い合わせ下さい。');
            afterCalculateByError('coin_tossing_spinner');
            clearProgressBar();
        }
    });
}

export function getReturnedDataIfExist()
{
    if(_.isNull(returned_data.status)) {
        return null;
    }
    return returned_data;
}

function displayText(data)
{
    if (data.progress_rate >= 10) {
        return data.progress_count + '/' + data.iteration;
    }
    return '';
}

function showErrorMessage(message) {
    $('#alert_danger_message').html(message);
    $('#alert_danger').show();
    let offset =  $('#alert_danger').offset().top - $('.kt-pagetitle').offset().top -20;
    $("html,body").animate({scrollTop: offset});
}

/**
 * ready状態から一定時間が経過した場合、ジョブが開始されなかったものとして処理を中断する。
 */
function isReadyTimeExceed()
{
    let current = new Date();
    return (current.getTime() - start_time.getTime()) > (ready_exceed_time * 1000);
}

export function clearProgressBar() {
    $('.calculate_progress-bar').hide();
}
