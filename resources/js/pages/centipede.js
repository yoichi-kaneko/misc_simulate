import {
    addLotteryRateElement,
    initLotteryRate,
    removeLastLotteryRateElement,
    resetLotteryRateElement
} from "../functions/lottery_rate";

initLotteryRate();

$('#add_lottery_block').click(function () {
    addLotteryRateElement();
});

$('#remove_lottery_block').click(function () {
    removeLastLotteryRateElement();
});

$('#reset_lottery').click(function () {
    resetLotteryRateElement();
});
