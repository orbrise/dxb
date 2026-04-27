/**
 * jQuery Plugin Polyfills
 * This file provides fallback functions for jQuery plugins that may not be loaded on all pages
 * Prevents "$.fn.pluginName is not a function" errors
 */

(function($) {
    'use strict';
    
    // Polyfill for select2 plugin
    if (typeof $.fn.select2 === 'undefined') {
        $.fn.select2 = function(options) {
            // Return this to maintain chainability
            return this;
        };
    }
    
    // Polyfill for typeahead plugin
    if (typeof $.fn.typeahead === 'undefined') {
        $.fn.typeahead = function() {
            // Return this to maintain chainability
            return this;
        };
    }
    
    // Polyfill for typeaheadCity plugin
    if (typeof $.fn.typeaheadCity === 'undefined') {
        $.fn.typeaheadCity = function() {
            // Return this to maintain chainability
            return this;
        };
    }
    
    // Also ensure data() returns empty object for these plugins when not loaded
    var originalData = $.fn.data;
    $.fn.data = function(key) {
        var result = originalData.apply(this, arguments);
        
        // Return safe defaults for plugin data requests
        if (result === undefined && typeof key === 'string') {
            if (key === 'select2' || key === 'ttTypeahead' || key === 'typeahead') {
                return null;
            }
        }
        
        return result;
    };
    
})(jQuery);
