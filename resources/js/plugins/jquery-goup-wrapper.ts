/// <reference path="../../../types/jquery-goup.d.ts" />
import jQuery from 'jquery';
import 'jquery-goup/src/jquery.goup.js';
import type { GoupOptions } from 'jquery-goup';

// TODO: jQueryのgoupプラグインからreactで類似する挙動をするコンポーネントに移行する
export default {
  init: function(options: GoupOptions) {
    // @ts-ignore
    return jQuery.goup(options);
  }
};
