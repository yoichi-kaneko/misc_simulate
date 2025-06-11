import jQuery from 'jquery';
import 'show-more/jquery.show-more.js';

interface ShowMoreOptions {
  [key: string]: any;
}

export default {
  init: function(selector: string | JQuery, options: ShowMoreOptions) {
    // @ts-ignore
    return jQuery(selector).showMore(options);
  }
};