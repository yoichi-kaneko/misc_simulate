import {initializeExponentialForms} from "./functions/exponential";

let parser = UAParser();
initializeAll();

Chart.plugins.register({
    beforeDraw: function(c){
        let ctx = c.chart.ctx;
        ctx.fillStyle = 'rgba(255,255,255,1)';
        ctx.fillRect(0, 0, c.chart.width, c.chart.height);
    }
});

$('.alert .close').click(function () {
    $('.alert_block').hide();
});

function initializeAll() {
    $('[data-toggle="tooltip"]').tooltip();
    $('span[data-popover-color="default"]').popover();
    $.goup();
    $('.select2').select2({
        minimumResultsForSearch: Infinity
    });

    $('.katex').each(function () {
        let element = $(this)[0];
        katex.render($(this).attr('expression'), element, {
            throwOnError: false
        });
    });

    let token = $('meta[name="csrf-token"]').attr('content');
    if (token) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    }
    let tab_page = $('#tablist').attr('page');
    if (tab_page) {
        $('a.nav-link').each(function (i, elem) {
            if($(this).attr('role') == tab_page) {
                $(this).addClass('active');
            }
        });
    }

    if (parser.browser.name == 'IE') {
        $('.chart_download').hide();
    }

    if ($('#participants_block').length > 0) {
        $('#coin_tossing_block').hide();
    }

    initializeExponentialForms();
}
