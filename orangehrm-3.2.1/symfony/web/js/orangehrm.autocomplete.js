/** 
 * Extend JQuery .autocomplete method to set default options.width to input elements innerWidth() property.
 * See: $.Autocompleter.Select.show() method in jquery.autocomplete.js
 * 
 * Using innerWidth fixes issue of autocomplete drop down width being less than the input width if input has 
 * any padding-left or padding-right.
 **/

(function($){
    var jqAutocomplete = $.fn.autocomplete;
    $.fn.autocomplete = function(urlOrData, options) {

        if (options.width === undefined || (typeof options.width != "string" && options.width <= 0)) {
            options.width = $(this).innerWidth();
        }
        return jqAutocomplete.call(this, urlOrData, options);
    };
}(jQuery));     