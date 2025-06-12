import 'jquery';

declare module 'jquery' {
  interface JQuery {
    /**
     * Initialise show-more plugin on a jQuery collection.
     * @param options Plugin options
     */
    showMore(options?: Record<string, any>): this;
  }
}