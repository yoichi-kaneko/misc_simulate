import jQuery from 'jquery';
import _ from 'underscore';
import UAParser from 'ua-parser-js';
import katex from 'katex';

// TODO: グローバル変数への登録を解消する
window.$ = window.jQuery = jQuery;
window._ = _;
window.UAParser = UAParser;
window.katex = katex;

// vite移行後のコード
import 'bootstrap/dist/js/bootstrap';
import 'bootstrap-notify';
import 'popper.js/dist/popper';
import 'chart.js';
import 'select2';
import 'datatables';
import 'datatables-responsive';
import 'jsrender';
