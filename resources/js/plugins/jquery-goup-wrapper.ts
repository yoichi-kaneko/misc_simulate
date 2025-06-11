import jQuery from 'jquery';
import 'jquery-goup/src/jquery.goup.js';

interface GoupOptions {
  [key: string]: any;
}

export default {
  init: function(options: GoupOptions) {
    // @ts-ignore
    return jQuery.goup(options);
  }
};