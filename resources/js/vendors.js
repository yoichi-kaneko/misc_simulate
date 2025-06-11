import jQuery from 'jquery';
import _ from 'underscore';
import UAParser from 'ua-parser-js';
import katex from 'katex';

// TODO: グローバル変数への登録を解消する
// Phase 1: New code should use explicit imports
// Phase 2: Refactor existing code gradually
// Phase 3: Remove global assignments
window.$ = window.jQuery = jQuery;
window._ = _;
window.UAParser = UAParser;
window.katex = katex;

// vite移行後のコード
import 'bootstrap/dist/js/bootstrap';
import 'bootstrap-notify';
// popper.js is imported through the wrapper in resources/js/plugins/popover-wrapper.js
import 'chart.js';
// jsrender is imported through the wrapper in resources/js/plugins/jsrender-wrapper.js
