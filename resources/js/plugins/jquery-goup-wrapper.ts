/// <reference path="../../../types/jquery-goup.d.ts" />
import jQuery from 'jquery';
import 'jquery-goup/src/jquery.goup.js';

interface GoupOptions {
  location?: 'left' | 'right';
  locationOffset?: number;
  bottomOffset?: number;
  containerSize?: number;
  containerRadius?: number;
  containerClass?: string;
  arrowClass?: string;
  containerColor?: string;
  arrowColor?: string;
  trigger?: number;
  entryAnimation?: 'fade' | 'slide';
  alwaysVisible?: boolean;
  goupSpeed?: number;
  hideUnderWidth?: number;
  title?: string;
  titleAsText?: boolean;
  titleAsTextClass?: string;
  zIndex?: number;
  [key: string]: any;
}

export default {
  init: function(options: GoupOptions) {
    return jQuery.goup(options);
  }
};
