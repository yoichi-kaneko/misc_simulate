import {
    addLotteryRateElement,
    initLotteryRate,
    removeLastLotteryRateElement,
    resetLotteryRateElement
} from "../functions/lottery_rate";
import {doLotteryCalculate} from "../render_chart/lottery/lottery";
import {beforeCalculate} from "../functions/calculate";

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

$('#calculate_lottery').click(function () {
    beforeCalculate('lottery_spinner');
    doLotteryCalculate();
});
