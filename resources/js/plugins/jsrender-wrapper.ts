// jsrender-wrapper.ts
// This is a wrapper for the jsrender library to handle its import properly
import $ from 'jquery';
import 'jsrender';

// Define interfaces for the types
interface JQueryTemplate extends JQuery {
  render(data: any): string;
}

// Create a wrapper object to expose jsrender functionality
const jsrenderWrapper = {
  /**
     * Render a template with the given data
     * @param {Object} template - The jQuery template object or selector string
     * @param {Object} data - The data to render the template with
     * @returns {String} The rendered HTML
     */
  render: function(template: JQueryTemplate | string, data: any): string {
    // Check if template is a jQuery object
    if (typeof template !== 'string' && 'jquery' in template) {
      return (template as JQueryTemplate).render(data);
    }
    // If it's a selector string, convert to jQuery object first
    return ($(template) as JQueryTemplate).render(data);
  }
};

export default jsrenderWrapper;