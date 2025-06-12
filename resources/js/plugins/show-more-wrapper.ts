/// <reference path="../../../types/jquery-show-more.d.ts" />
import jQuery from 'jquery';
import 'show-more/jquery.show-more.js';

interface ShowMoreOptions {
  [key: string]: any;
}

export default {
  init: function(selector: string | JQuery, options: ShowMoreOptions) {
    return jQuery(selector).showMore(options);
  }
};
