// popover-wrapper.js
// This is a wrapper for the popover functionality from bootstrap

import 'popper.js/dist/popper';
import 'bootstrap/js/dist/popover';

// Create a wrapper object to expose popover functionality
const popoverWrapper = {
  /**
     * Initialize popover for the given elements
     * @param {String|Object} selector - jQuery selector or object
     * @param {Object} options - Options for the popover
     * @returns {Object} - jQuery object for chaining
     */
  init: function(selector, options = {}) {
    return $(selector).popover(options);
  },

  /**
     * Show the popover for the given element
     * @param {String|Object} selector - jQuery selector or object
     * @returns {Object} - jQuery object for chaining
     */
  show: function(selector) {
    return $(selector).popover('show');
  },

  /**
     * Hide the popover for the given element
     * @param {String|Object} selector - jQuery selector or object
     * @returns {Object} - jQuery object for chaining
     */
  hide: function(selector) {
    return $(selector).popover('hide');
  },

  /**
     * Toggle the popover for the given element
     * @param {String|Object} selector - jQuery selector or object
     * @returns {Object} - jQuery object for chaining
     */
  toggle: function(selector) {
    return $(selector).popover('toggle');
  },

  /**
     * Dispose the popover for the given element
     * @param {String|Object} selector - jQuery selector or object
     * @returns {Object} - jQuery object for chaining
     */
  dispose: function(selector) {
    return $(selector).popover('dispose');
  }
};

export default popoverWrapper;