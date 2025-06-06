// jsrender-wrapper.js
// This is a wrapper for the jsrender library to handle its import properly
import $ from 'jquery';
import 'jsrender';

// Create a wrapper object to expose jsrender functionality
const jsrenderWrapper = {
    /**
     * Render a template with the given data
     * @param {Object} template - The jQuery template object
     * @param {Object} data - The data to render the template with
     * @returns {String} The rendered HTML
     */
    render: function(template, data) {
        // Check if template is a jQuery object
        if (template.jquery) {
            return template.render(data);
        }
        // If it's a selector string, convert to jQuery object first
        return $(template).render(data);
    }
};

export default jsrenderWrapper;