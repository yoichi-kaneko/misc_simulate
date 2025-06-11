import goupWrapper from './plugins/jquery-goup-wrapper';
import popoverWrapper from './plugins/popover-wrapper';

let parser = UAParser();
initializeAll();


$('.alert .close').click(function () {
  $('.alert_block').hide();
});

function initializeAll() {
  // $('[data-toggle="tooltip"]').tooltip();
  popoverWrapper.init('span[data-popover-color="default"]');
  goupWrapper.init();
  /*
    $('.select2').select2({
        minimumResultsForSearch: Infinity
    });*/

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
}
