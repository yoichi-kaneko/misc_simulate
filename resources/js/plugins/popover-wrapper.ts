// popover-wrapper.ts
// This is a wrapper for the popover functionality from bootstrap

import $ from 'jquery';
import 'popper.js/dist/popper';
import 'bootstrap/js/dist/popover';

// Define interfaces for the types
interface PopoverOptions {
  [key: string]: any;
}

// Create a wrapper object to expose popover functionality
const popoverWrapper = {
  /**
     * Initialize popover for the given elements
     * @param {String|Object} selector - jQuery selector or object
     * @param {Object} options - Options for the popover
     * @returns {Object} - jQuery object for chaining
     */
  init: function(selector: string | JQuery, options: PopoverOptions = {}): JQuery {
    return $(selector).popover(options);
  },

  /**
     * Show the popover for the given element
     * @param {String|Object} selector - jQuery selector or object
     * @returns {Object} - jQuery object for chaining
     */
  show: function(selector: string | JQuery): JQuery {
    return $(selector).popover('show');
  },

  /**
     * Hide the popover for the given element
     * @param {String|Object} selector - jQuery selector or object
     * @returns {Object} - jQuery object for chaining
     */
  hide: function(selector: string | JQuery): JQuery {
    return $(selector).popover('hide');
  },

  /**
     * Toggle the popover for the given element
     * @param {String|Object} selector - jQuery selector or object
     * @returns {Object} - jQuery object for chaining
     */
  toggle: function(selector: string | JQuery): JQuery {
    return $(selector).popover('toggle');
  },

  /**
     * Dispose the popover for the given element
     * @param {String|Object} selector - jQuery selector or object
     * @returns {Object} - jQuery object for chaining
     */
  dispose: function(selector: string | JQuery): JQuery {
    return $(selector).popover('dispose');
  }
};

export default popoverWrapper;