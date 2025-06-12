/// <reference path="../../../types/jquery-show-more.d.ts" />
import jQuery from 'jquery';
import 'show-more/jquery.show-more.js';
import type { ShowMoreOptions } from 'jquery-show-more';

// TODO: jQueryのshowmoreプラグインからreactで類似する挙動をするコンポーネントに移行する
export default {
  init: function(selector: string | JQuery, options?: ShowMoreOptions): JQuery {
    // @ts-ignore
    return jQuery(selector).showMore(options);
  }
};
