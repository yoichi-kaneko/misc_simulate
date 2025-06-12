import 'jquery';

interface ShowMoreOptions {
  [key: string]: any;
}

declare module 'jquery' {
  interface JQuery {
    /**
     * Initialise show-more plugin on a jQuery collection.
     * @param options Plugin options
     */
    showMore(options?: Record<string, any>): this;
  }
}