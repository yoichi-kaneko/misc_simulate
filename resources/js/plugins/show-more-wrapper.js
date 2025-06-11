import jQuery from 'jquery';
import 'show-more/jquery.show-more.js';

export default {
  init: function(selector, options) {
    return jQuery(selector).showMore(options);
  }
};
