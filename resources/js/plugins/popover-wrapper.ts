// popover-wrapper.ts
// This is a wrapper for the popover functionality from bootstrap

import $ from 'jquery';
import 'popper.js/dist/popper';
import 'bootstrap/js/dist/popover';

// Define interfaces for the types
interface PopoverOptions {
  /**
   * Default content value if `data-content` attribute isn't present.
   */
  content?: string | Element | ((element: Element) => string);

  /**
   * How to position the popover - auto | top | bottom | left | right.
   * When "auto" is specified, it will dynamically reorient the popover.
   */
  placement?: 'auto' | 'top' | 'bottom' | 'left' | 'right' | (() => string);

  /**
   * Appends the popover to a specific element.
   */
  container?: string | Element | false;

  /**
   * Delay showing and hiding the popover (ms).
   * If a number is supplied, delay is applied to both hide/show.
   * Object structure is: delay: { "show": 500, "hide": 100 }
   */
  delay?: number | { show: number; hide: number };

  /**
   * Insert HTML into the popover. If false, jQuery's text method will be used to insert
   * content into the DOM. Use text if you're worried about XSS attacks.
   */
  html?: boolean;

  /**
   * Selector for the element to serve as the popover's container.
   */
  selector?: string | false;

  /**
   * Base HTML to use when creating the popover.
   */
  template?: string;

  /**
   * Default title value if `title` attribute isn't present.
   */
  title?: string | Element | ((element: Element) => string);

  /**
   * How popover is triggered - click | hover | focus | manual.
   * You may pass multiple triggers; separate them with a space.
   */
  trigger?: 'click' | 'hover' | 'focus' | 'manual' | string;

  /**
   * Offset of the popover relative to its target.
   */
  offset?: number | string;

  /**
   * Allow to specify which position Popper will use on fallback.
   */
  fallbackPlacement?: string | string[];

  /**
   * Overflow constraint boundary of the popover.
   * Accepts the values of 'viewport', 'window', 'scrollParent',
   * or an HTMLElement reference (JavaScript only).
   */
  boundary?: 'viewport' | 'window' | 'scrollParent' | Element;

  /**
   * Enable or disable the sanitization. If activated 'template', 'content' and 'title' will be sanitized.
   */
  sanitize?: boolean;

  /**
   * Object which contains allowed attributes and tags.
   */
  whiteList?: Record<string, string[]>;

  /**
   * Here you can supply your own sanitize function.
   */
  sanitizeFn?: (content: string) => string;

  /**
   * To change Bootstrap's default Popper.js config.
   */
  popperConfig?: Record<string, unknown>;

  /**
   * Custom color for the popover.
   */
  popoverColor?: string;
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
