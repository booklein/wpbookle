/**
 * An initialization wrapper for Ajax Search Pro
 *
 * This solution gets rid off the nasty inline script declarations once and for all.
 * Instead the search instance params are stored in a hidden div element. This baby here
 * parses through them and does a very simple initialization process.
 * Also, the ASP variable now provides a way for developers to manually initialize the instances
 * anytime, anywhere.
 */

// Use the window to make sure it is in the main scope, I do not trust IE
window.ASP = window.ASP || {};

window.ASP.getScope = function() {
    /**
     * Explanation:
     * If the sript is scoped, the first argument is always passed in a localized jQuery
     * variable, while the actual parameter can be aspjQuery or jQuery (or anything) as well.
     */
    if (typeof jQuery !== "undefined") return jQuery;

    // The code should never reach this point, but sometimes magic happens (unloaded or undefined jQuery??)
    // .. I am almost positive at this point this is going to fail anyways, but worth a try.
    if (typeof window[ASP.js_scope] !== "undefined")
        return window[ASP.js_scope];
    else
        return eval(ASP.js_scope);
};

// Call this function if you need to initialize an instance that is printed after an AJAX call
// Calling without an argument initializes all instances found.
window.ASP.initialize = function(id) {
    // Yeah I could use $ or jQuery as the scope variable, but I like to avoid magical errors..
    var scope = window.ASP.getScope();
    var selector = ".asp_init_data";

    if (typeof id !== 'undefined')
        selector = "div[id*=asp_init_id_" + id + "]";

    /**
     * Getting around inline script declarations with this solution.
     * So these new, invisible divs contains a JSON object with the parameters.
     * Parse all of them and do the declaration.
     */
    scope(selector).each(function(index, value){
        var rid =  scope(this).attr('id').match(/^asp_init_id_(.*)/)[1];
        var jsonData = scope(this).html().trim();
        if (typeof jsonData === "undefined") return false;

        var args = JSON.parse(jsonData);

        return scope("#ajaxsearchpro" + rid).ajaxsearchpro(args);
    });
};

window.ASP.ready = function() {
    var scope = window.ASP.getScope();

    scope(document).ready(function () {
        window.ASP.initialize();
    });
};

// Do the init here
window.ASP.ready();